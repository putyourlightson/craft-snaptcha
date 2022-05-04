<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\models;

use craft\base\Model;

class SettingsModel extends Model
{
    /**
     * @var bool
     */
    public bool $validationEnabled = false;

    /**
     * @var bool
     */
    public bool $validateUsers = true;

    /**
     * @var bool
     */
    public bool $oneTimeKey = true;

    /**
     * @var bool
     */
    public bool $logRejected = true;

    /**
     * @var string
     */
    public string $fieldName = 'snaptcha';

    /**
     * @var string
     */
    public string $salt = '';

    /**
     * @var string
     */
    public string $errorTitle = 'Error';

    /**
     * @var string
     */
    public string $errorMessage = 'For some reason your submission was flagged as spam. Please click the button below to resubmit your data.';

    /**
     * @var string
     */
    public string $errorJavascriptDisabled = 'JavaScript must be enabled for your submission to be verified. Please enable it and refresh the page.';

    /**
     * @var string
     */
    public string $errorButtonText = 'Resubmit';

    /**
     * @var string
     */
    public string $errorTemplate = '';

    /**
     * @var int
     */
    public int $minimumSubmitTime = 1;

    /**
     * @var int
     */
    public int $expirationTime = 60;

    /**
     * @var array|string
     */
    public string|array $excludeControllerActions = [];

    /**
     * @var array|string
     */
    public string|array $allowList = [];

    /**
     * @var array|string
     */
    public string|array $denyList = [];

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return [
            [['fieldName', 'errorTitle', 'errorMessage', 'errorJavascriptDisabled', 'errorButtonText', 'expirationTime', 'minimumSubmitTime'], 'required'],
            [['validationEnabled', 'oneTimeKey', 'logRejected'], 'boolean'],
            [['fieldName', 'errorTitle', 'errorMessage', 'errorJavascriptDisabled', 'errorButtonText'], 'string'],
            [['expirationTime', 'minimumSubmitTime'], 'integer'],
        ];
    }
}
