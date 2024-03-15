<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\models;

use craft\base\Model;

class SnaptchaModel extends Model
{
    /**
     * @var string|null
     */
    public ?string $key = null;

    /**
     * @var string|null
     */
    public ?string $value = null;

    /**
     * @var string|null
     */
    public ?string $ipAddress = null;

    /**
     * @var int|null
     */
    public ?int $timestamp = null;

    /**
     * @var int|null
     */
    public ?int $expirationTime = null;

    /**
     * @var int|null
     */
    public ?int $minimumSubmitTime = null;

    /**
     * @var string|null
     */
    public ?string $nonce = null;

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return [
            [['key', 'value', 'ipAddress'], 'required'],
            [['timestamp', 'expirationTime', 'minimumSubmitTime'], 'integer'],
        ];
    }
}
