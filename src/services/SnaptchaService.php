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
use yii\base\Action;
use yii\base\Event;

/**
 *
 * @property-read array $postedValues
 */
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
        'complete-cookie-consent/consent/submit',
        'cookie-consent/consent/update',
        'graphql/api',
    ];

    /**
     * Returns a field key.
     *
     * @param SnaptchaModel $model
     * @return string|null
     */
    public function getFieldKey(SnaptchaModel $model)
    {
        $record = $this->_getSnaptchaRecord($model);

        return $record ? $record->key : null;
    }

    /**
     * Returns a field value.
     *
     * @param SnaptchaModel $model
     * @return string|null
     */
    public function getFieldValue(SnaptchaModel $model)
    {
        $record = $this->_getSnaptchaRecord($model);

        return $record ? $record->value : null;
    }

    public function getPostedValues(): array
    {
        $values = Craft::$app->request->getBodyParams();

        if (isset($values[Snaptcha::$plugin->settings->fieldName])) {
            unset($values[Snaptcha::$plugin->settings->fieldName]);
        }

        return $this->_flattenValues($values);
    }

    /**
     * Returns whether the controller action is excluded from validation.
     *
     * @param Action $action
     * @return bool
     */
    public function isExcludedControllerAction(Action $action): bool
    {
        if (!Craft::$app->getRequest()->getIsActionRequest()) {
            return false;
        }

        $controllerAction = $action->getUniqueId();

        // Fire a before event
        $event = new ValidateFieldEvent(['excludeControllerActions' => self::EXCLUDE_CONTROLLER_ACTIONS]);
        $this->trigger(self::EVENT_BEFORE_EXCLUDE_CONTROLLER_ACTIONS, $event);

        if (in_array($controllerAction, $event->excludeControllerActions)) {
            return true;
        }

        $excludeControllerActions = $this->_getNormalizedArray(Snaptcha::$plugin->settings->excludeControllerActions);

        if (in_array($controllerAction, $excludeControllerActions)) {
            return true;
        }

        return false;
    }

    /**
     * Returns whether the user is allowed.
     *
     * @return bool
     */
    public function isUserAllowed(): bool
    {
        return !Snaptcha::$plugin->settings->validateUsers && Craft::$app->getUser()->getIsGuest() === false;
    }

    /**
     * Returns whether the IP address is allowed.
     *
     * @return bool
     */
    public function isIpAllowed(): bool
    {
        $allowList = $this->_getNormalizedArray(Snaptcha::$plugin->settings->allowList);

        return in_array(Craft::$app->getRequest()->getUserIP(), $allowList);
    }

    /**
     * Returns whether the IP address is denied.
     *
     * @return bool
     */
    public function isIpDenied(): bool
    {
        $denyList = $this->_getNormalizedArray(Snaptcha::$plugin->settings->denyList);

        return in_array(Craft::$app->getRequest()->getUserIP(), $denyList);
    }

    /**
     * Returns whether a record is expired.
     *
     * @param SnaptchaRecord $record
     * @return bool
     */
    public function isExpired(SnaptchaRecord $record): bool
    {
        return $record->timestamp + ($record->expirationTime * 60) < time();
    }

    /**
     * Returns whether a record is submitted too soon.
     *
     * @param SnaptchaRecord $record
     * @return bool
     */
    public function isTooSoon(SnaptchaRecord $record): bool
    {
        return $record->timestamp + $record->minimumSubmitTime > time();
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

        // Check if user is allowed
        if ($this->isUserAllowed()) {
            return true;
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
                'value' => $value,
                'ipAddress' => $this->_getHashedIpAddress(),
            ])
            ->one();

        if ($record === null) {
            $this->_reject('Value not found in database.');

            return false;
        }

        // Check if field has expired
        if ($this->isExpired($record)) {
            $this->_reject('Expiration time of {minutes} minute(s) has passed.', ['minutes' => $record->expirationTime]);

            return false;
        }

        // Check if minimum submit time has not passed
        if ($this->isTooSoon($record)) {
            $this->_reject('Minimum submit time of {second} second(s) has not yet passed.', ['second' => $record->minimumSubmitTime]);

            return false;
        }

        // Check if record should be deleted
        if (Snaptcha::$plugin->settings->oneTimeKey) {
            $record->delete();
        }

        // Delete all expired records
        SnaptchaRecord::deleteAll([
            '<', 'timestamp', time() - (Snaptcha::$plugin->settings->expirationTime * 60)
        ]);

        // Fire an after event
        if ($this->hasEventHandlers(self::EVENT_AFTER_VALIDATE_FIELD)) {
            $this->trigger(self::EVENT_AFTER_VALIDATE_FIELD, new Event());
        }

        return true;
    }

    /**
     * Returns a Snaptcha record.
     *
     * @param SnaptchaModel $model
     * @return SnaptchaRecord|null
     */
    private function _getSnaptchaRecord(SnaptchaModel $model)
    {
        $hashedIpAddress = $this->_getHashedIpAddress();

        // Get most recent record with IP address from DB
        /** @var SnaptchaRecord|null $record */
        $record = SnaptchaRecord::find()
            ->where(['ipAddress' => $hashedIpAddress])
            ->orderBy('timestamp desc')
            ->one();

        // If record does not exist or one time key is enabled or the expiration time has passed
        if ($record === null || Snaptcha::$plugin->settings->oneTimeKey || $this->isExpired($record)) {
            // Set key to random string
            $model->key = StringHelper::randomString(16);
            $model->value = $this->_getHashedValue($model->key, Snaptcha::$plugin->settings->salt);

            // Hash IP address for privacy
            $model->ipAddress = $hashedIpAddress;

            // Set timestamp to current time
            $model->timestamp = time();

            // Set optional fields from settings if not defined
            $model->expirationTime = $model->expirationTime ?? Snaptcha::$plugin->settings->expirationTime;
            $model->minimumSubmitTime = $model->minimumSubmitTime ?? Snaptcha::$plugin->settings->minimumSubmitTime;

            if (!$model->validate()) {
                return null;
            }

            $record = new SnaptchaRecord($model);
        }

        // Refresh timestamp
        $record->timestamp = time();

        if (!$record->save()) {
            return null;
        }

        return $record;
    }

    /**
     * Returns the hashed value.
     *
     * @param string $key
     * @param string $salt
     * @return string
     */
    private function _getHashedValue(string $key, string $salt): string
    {
        return base64_encode($key.$salt);
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
     * Returns a normalized array of values.
     *
     * @param array|string $values
     * @return array
     */
    private function _getNormalizedArray($values): array
    {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                // Normalize to string
                if (is_array($value)) {
                    $value = $value[0];
                }

                $values[$key] = trim($value, " \/");
            }
        }
        else {
            $values = [];
        }

        return $values;
    }

    /**
     * Flattens a multi-dimensional array of values to a flat array that can
     * be used to output hidden fields, preserving the keys.
     *
     * @param array $values
     * @param string $currentKey
     * @return array
     */
    private function _flattenValues(array $values, string $currentKey = ''): array
    {
        $flattened = [];

        foreach ($values as $key => $value) {
            $key = $currentKey ? $currentKey.'['.$key.']' : $key;

            if (is_array($value)) {
                $flattened = array_merge($flattened, $this->_flattenValues($value, $key));
            }
            else {
                $flattened[$key] = $value;
            }
        }

        return $flattened;
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
