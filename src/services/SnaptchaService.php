<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\services;

use Craft;
use craft\base\Component;
use craft\helpers\StringHelper;
use putyourlightson\logtofile\LogToFile;
use putyourlightson\snaptcha\events\ValidateFieldEvent;
use putyourlightson\snaptcha\models\SnaptchaModel;
use putyourlightson\snaptcha\records\SnaptchaRecord;
use putyourlightson\snaptcha\Snaptcha;
use yii\base\Event;

class SnaptchaService extends Component
{
    /**
     * @event ValidateFieldEvent
     */
    const EVENT_BEFORE_EXCLUDE_CONTROLLER_ACTIONS = 'beforeExcludeControllerActions';

    /**
     * @event ValidateFieldEvent
     */
    const EVENT_BEFORE_VALIDATE_FIELD = 'beforeValidateField';

    /**
     * @event Event
     */
    const EVENT_AFTER_VALIDATE_FIELD = 'afterValidateField';

    /**
     * @const string[]
     */
    const EXCLUDE_CONTROLLER_ACTIONS = [
        'commerce/webhooks/process-webhook',
        'cookie-consent/consent/update',
        'complete-cookie-consent/consent/submit',
    ];

    /**
     * Returns a field value.
     *
     * @param SnaptchaModel $model
     *
     * @return string|null
     */
    public function getFieldValue(SnaptchaModel $model)
    {
        $now = time();
        $hashedIpAddress = $this->_getHashedIpAddress();

        // Get most recent record with IP address from DB
        /** @var SnaptchaRecord|null $record */
        $record = SnaptchaRecord::find()
            ->where(['ipAddress' => $hashedIpAddress])
            ->orderBy('timestamp desc')
            ->one();

        // If record does not exist or one time key is enabled or the expiration time has passed
        if ($record === null || Snaptcha::$plugin->settings->oneTimeKey || $record->timestamp + ($record->expirationTime * 60) < $now) {
            // Set key to random string
            $model->key = StringHelper::randomString();

            // Hash IP address for privacy
            $model->ipAddress = $hashedIpAddress;

            // Set timestamp to current time
            $model->timestamp = $now;

            // Set optional fields from settings if not defined
            $model->expirationTime = $model->expirationTime ?? Snaptcha::$plugin->settings->expirationTime;
            $model->minimumSubmitTime = $model->minimumSubmitTime ?? Snaptcha::$plugin->settings->minimumSubmitTime;

            if (!$model->validate()) {
                return null;
            }

            $record = new SnaptchaRecord($model);
        }

        // Refresh timestamp
        $record->timestamp = $now;

        if (!$record->save()) {
            return null;
        }

        return $record->key;
    }

    /**
     * Returns whether the controller action is excluded from validation.
     *
     * @return bool
     */
    public function isExcludedControllerAction(): bool
    {
        if (!Craft::$app->getRequest()->getIsActionRequest()) {
            return false;
        }

        $controllerAction = implode('/', Craft::$app->getRequest()->getActionSegments());

        // Fire a before event
        $event = new ValidateFieldEvent(['excludeControllerActions' => self::EXCLUDE_CONTROLLER_ACTIONS]);
        $this->trigger(self::EVENT_BEFORE_EXCLUDE_CONTROLLER_ACTIONS, $event);

        return in_array($controllerAction, $event->excludeControllerActions);
    }

    /**
     * Returns whether the URI is excluded from validation.
     *
     * @param string $uri
     *
     * @return bool
     */
    public function isExcludedUri(string $uri): bool
    {
        if (is_array(Snaptcha::$plugin->settings->excludedUriPatterns)) {
            foreach (Snaptcha::$plugin->settings->excludedUriPatterns as $uriPattern) {
                // Normalize to string
                if (is_array($uriPattern)) {
                    $uriPattern = $uriPattern[0];
                }

                // Trim slashes
                $uriPattern = trim($uriPattern, '/');

                // Escape delimiter
                $uriPattern = str_replace('/', '\/', $uriPattern);

                if (preg_match('/'.$uriPattern.'/', trim($uri, '/'))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns whether the IP address is allowed.
     *
     * @return bool
     */
    public function isIpAllowed(): bool
    {
        return $this->isIpInList(
            Craft::$app->getRequest()->getUserIP(),
            Snaptcha::$plugin->settings->allowList
        );
    }

    /**
     * Returns whether the IP address is denied.
     *
     * @return bool
     */
    public function isIpDenied(): bool
    {
        return $this->isIpInList(
            Craft::$app->getRequest()->getUserIP(),
            Snaptcha::$plugin->settings->denyList
        );
    }

    /**
     * Returns whether the IP address is blocked.
     *
     * @param string $ipAddress
     * @param array $ipList
     * @return bool
     */
    public function isIpInList(string $ipAddress, array $ipList): bool
    {
        if (is_array($ipList)) {
            foreach ($ipList as $ip) {
                // Normalize to string
                if (is_array($ip)) {
                    $ip = $ip[0];
                }

                if ($ipAddress == trim($ip)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validates a submitted field.
     *
     * @param string|null $value
     *
     * @return bool
     */
    public function validateField(string $value = null): bool
    {
        // Fire a before event
        $event = new ValidateFieldEvent(['value' => $value]);
        $this->trigger(self::EVENT_BEFORE_VALIDATE_FIELD, $event);

        if ($event->skipValidation) {
            return true;
        }

        if (!$event->isValid) {
            return false;
        }

        // Check if IP address is allowed
        if ($this->isIpAllowed()) {
            return true;
        }

        if ($value === null) {
            $this->_reject('Value submitted is null.');

            return false;
        }

        // Check if IP address is denied
        if ($this->isIpDenied()) {
            $this->_reject('IP address was denied.');

            return false;
        }

        /** @var SnaptchaRecord|null $record */
        $record = SnaptchaRecord::find()
            ->where([
                'key' => $value,
                'ipAddress' => $this->_getHashedIpAddress(),
            ])
            ->one();

        if ($record === null) {
            $this->_reject('Value not found in database.');

            return false;
        }

        $now = time();

        // Check if field has expired
        if ($record->timestamp + ($record->expirationTime * 60) < $now) {
            $this->_reject('Expiration time of {minutes} minute(s) has passed.', ['minutes' => $record->expirationTime]);

            return false;
        }

        // Check if minimum submit time has not passed
        if ($record->timestamp + $record->minimumSubmitTime > $now) {
            $this->_reject('Minimum submit time of {second} second(s) has not yet passed.', ['second' => $record->minimumSubmitTime]);

            return false;
        }

        // Check if record should be deleted
        if (Snaptcha::$plugin->settings->oneTimeKey) {
            $record->delete();
        }

        // Delete all expired records
        SnaptchaRecord::deleteAll([
            '<', 'timestamp', $now - ($record->expirationTime * 60)
        ]);

        // Fire an after event
        if ($this->hasEventHandlers(self::EVENT_AFTER_VALIDATE_FIELD)) {
            $this->trigger(self::EVENT_AFTER_VALIDATE_FIELD, new Event());
        }

        return true;
    }

    /**
     * Returns the current user's hashed IP address.
     *
     * @return string
     */
    private function _getHashedIpAddress(): string
    {
        $ipAddress = Craft::$app->getRequest()->getUserIP();

        return $ipAddress === null ? '' : md5($ipAddress);
    }

    /**
     * Rejects and logs a form submission.
     *
     * @param string $message
     * @param array $params
     */
    private function _reject(string $message, array $params = [])
    {
        if (Snaptcha::$plugin->settings->logRejected) {
            $url = Craft::$app->getRequest()->getAbsoluteUrl();
            $message = Craft::t('snaptcha', $message, $params).' ['.$url.']';
            LogToFile::log($message, 'snaptcha');
        }
    }
}
