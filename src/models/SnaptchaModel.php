<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\models;

use craft\base\Model;

class SnaptchaModel extends Model
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $value;

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

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['key', 'value', 'ipAddress'], 'required'],
            [['timestamp', 'expirationTime', 'minimumSubmitTime'], 'integer'],
        ];
    }
}
