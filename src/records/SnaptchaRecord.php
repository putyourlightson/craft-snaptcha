<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\records;

use craft\db\ActiveRecord;

/**
 * Snaptcha Record
 *
 * @property string $key
 * @property string $ipAddress
 * @property int $timestamp
 * @property int|null $expirationTime
 * @property int|null $minimumSubmitTime
 */
class SnaptchaRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

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
