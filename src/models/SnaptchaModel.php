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
    public string $key;

    /**
     * @var string
     */
    public string $value;

    /**
     * @var string
     */
    public string $ipAddress;

    /**
     * @var int
     */
    public int $timestamp;

    /**
     * @var int|null
     */
    public ?int $expirationTime;

    /**
     * @var int|null
     */
    public ?int $minimumSubmitTime;

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['key', 'value', 'ipAddress'], 'required'],
            [['timestamp', 'expirationTime', 'minimumSubmitTime'], 'integer'],
        ];
    }
}
