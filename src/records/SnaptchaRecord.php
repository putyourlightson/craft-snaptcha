<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\records;

use craft\db\ActiveRecord;

/**
 * @property int $id
 * @property string $key
 * @property string $ipAddress
 * @property int $timestamp
 * @property int|null $expirationTime
 * @property int|null $minimumSubmitTime
 */
class SnaptchaRecord extends ActiveRecord
{
     /**
     * @inheritdoc
     *
     * @return string the table name
     */
    public static function tableName(): string
    {
        return '{{%snaptcha}}';
    }
}
