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
    public $validationEnabled = false;

    /**
     * @var bool
     */
    public $validateUsers = true;

    /**
     * @var bool
     */
    public $oneTimeKey = true;

    /**
     * @var bool
     */
    public $logRejected = true;

    /**
     * @var string
     */
    public $fieldName = 'snaptcha';

    /**
     * @var string
     */
    public $salt = '';

    /**
     * @var string
     */
    public $errorTitle = 'Error';

    /**
     * @var string
     */
    public $errorMessage = 'For some reason your submission was flagged as spam. Please click the button below to resubmit your data.';

    /**
     * @var string
     */
    public $errorJavascriptDisabled = 'JavaScript must be enabled for your submission to be verified. Please enable it and refresh the page.';

    /**
     * @var string
     */
    public $errorButtonText = 'Resubmit';

    /**
     * @var string
     */
    public $errorTemplate = '';

    /**
     * @var int
     */
    public $minimumSubmitTime = 1;

    /**
     * @var int
     */
    public $expirationTime = 60;

    /**
     * @var array|string
     */
    public $excludeControllerActions = [];

    /**
     * @var array|string
     */
    public $allowList = [];

    /**
     * @var array|string
     */
    public $denyList = [];

    /**
     * Keep this around so migrations don't break!
     *
     * @var array
     * @depracated
     */
    public $blacklist = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['fieldName', 'errorTitle', 'errorMessage', 'errorJavascriptDisabled', 'errorButtonText', 'expirationTime', 'minimumSubmitTime'], 'required'],
            [['validationEnabled', 'oneTimeKey', 'logRejected'], 'boolean'],
            [['fieldName', 'errorTitle', 'errorMessage', 'errorJavascriptDisabled', 'errorButtonText'], 'string'],
            [['expirationTime', 'minimumSubmitTime'], 'integer'],
        ];
    }
}
