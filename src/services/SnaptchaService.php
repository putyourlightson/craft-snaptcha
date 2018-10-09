<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\services;

use Craft;
use craft\base\Component;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use putyourlightson\snaptcha\events\ValidateFieldEvent;
use putyourlightson\snaptcha\models\SettingsModel;
use putyourlightson\snaptcha\models\SnaptchaModel;
use putyourlightson\snaptcha\records\SnaptchaRecord;
use putyourlightson\snaptcha\Snaptcha;
use yii\base\Event;

/**
 * Snaptcha Service
 */
class SnaptchaService extends Component
{
    // Constants
    // =========================================================================

    /**
     * @event ValidateFieldEvent
     */
    const EVENT_BEFORE_VALIDATE_FIELD = 'beforeValidateField';

    /**
     * @event Event
     */
    const EVENT_AFTER_VALIDATE_FIELD = 'afterValidateField';

    // Properties
    // =========================================================================

    /**
     * @var SettingsModel
     */
    private $_settings;

    // Public Methods
    // =========================================================================

    /**
    * Init
    */
    public function init()
    {
        parent::init();

        // Get settings
        $this->_settings = Snaptcha::$plugin->getSettings();
    }

    /**
     * Returns a field value
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
        if ($record === null || $this->_settings->oneTimeKey || $record->timestamp + ($record->expirationTime * 60) < $now) {
            // Set key to random string
            $model->key = StringHelper::randomString();

            // Hash IP address for privacy
            $model->ipAddress = $hashedIpAddress;

            // Set timestamp to current time
            $model->timestamp = $now;

            // Set optional fields from settings if not defined
            $model->expirationTime = $model->expirationTime ?? $this->_settings->expirationTime;
            $model->minimumSubmitTime = $model->minimumSubmitTime ?? $this->_settings->minimumSubmitTime;

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
     * Returns whether the URI is excluded from validation
     *
     * @param string $uri
     *
     * @return bool
     */
    public function isExcludedUri(string $uri): bool
    {
        if (is_array($this->_settings->excludedUriPatterns)) {
            foreach ($this->_settings->excludedUriPatterns as $excludedUriPattern) {
                // Normalize to string
                if (is_array($excludedUriPattern)) {
                    $excludedUriPattern = $excludedUriPattern[0];
                }

                if (preg_match('#'.trim($excludedUriPattern, '/').'#', $uri)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns whether the IP address is blacklisted
     *
     * @param string $ipAddress
     *
     * @return bool
     */
    public function isBlacklisted(string $ipAddress): bool
    {
        if (is_array($this->_settings->blacklist)) {
            foreach ($this->_settings->blacklist as $blacklistedIp) {
                // Normalize to string
                if (is_array($blacklistedIp)) {
                    $blacklistedIp = $blacklistedIp[0];
                }

                if ($ipAddress == trim($blacklistedIp)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validates a submitted field
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

        if ($value === null) {
            $this->_reject('Value submitted is null.');
            return false;
        }

        // Check if IP address is blacklisted
        if ($this->isBlacklisted(Craft::$app->getRequest()->getUserIP()) === true) {
            $this->_reject('IP address is blacklisted.');
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
            $this->_reject('Expiration time of '.$record->expirationTime.' minute(s) has passed.');
            return false;
        }

        // Check if minimum submit time has not passed
        if ($record->timestamp + $record->minimumSubmitTime > $now) {
            $this->_reject('Minimum submit time of '.$record->minimumSubmitTime.' second(s) has not yet passed.');
            return false;
        }

        // Check if record should be deleted
        if ($this->_settings->oneTimeKey) {
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

    // Private Methods
    // =========================================================================

    /**
     * Returns the current user's hashed IP address
     *
     * @return string
     */
    private function _getHashedIpAddress(): string
    {
        $ipAddress = Craft::$app->getRequest()->getUserIP();

        return $ipAddress === null ? '' : md5($ipAddress);
    }

    /**
     * Rejects and logs a form submission
     *
     * @param string $message
     */
    private function _reject(string $message)
    {
        if ($this->_settings->logRejected) {
            $file = Craft::getAlias('@storage/logs/snaptcha.log');
            $log = date('Y-m-d H:i:s').' '.$message."\n";

            FileHelper::writeToFile($file, $log, ['append' => true]);
        }
    }
}
