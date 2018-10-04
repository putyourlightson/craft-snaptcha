<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\models;

use craft\base\Model;

/**
 * Snaptcha Model
 */
class SnaptchaModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var int
     */
    public $timestamp;

    /**
     * @var int|null
     */
    public $expirationTime;

    /**
     * @var int|null
     */
    public $minimumSubmitTime;


    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['key', 'ipAddress'], 'required'],
            [['timestamp', 'expirationTime', 'minimumSubmitTime'], 'integer'],
        ];
    }
}
