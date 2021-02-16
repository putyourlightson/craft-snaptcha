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
    public $errorMessage = 'Sorry, you have failed the security test. Please ensure that you have javascript enabled and that you refresh the page that you are trying to submit.';

    /**
     * @var int
     */
    public $expirationTime = 60;

    /**
     * @var int
     */
    public $minimumSubmitTime = 3;

    /**
     * @var array
     */
    public $excludedUriPatterns = [];

    /**
     * @var array
     */
    public $allowList = [];

    /**
     * @var array
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
            [['fieldName', 'expirationTime', 'minimumSubmitTime', 'errorMessage'], 'required'],
            [['validationEnabled', 'oneTimeKey', 'logRejected'], 'boolean'],
            [['fieldName', 'errorMessage'], 'string'],
            [['expirationTime', 'minimumSubmitTime'], 'integer'],
        ];
    }
}
