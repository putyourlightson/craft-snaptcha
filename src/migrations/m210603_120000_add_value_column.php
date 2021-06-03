<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\migrations;

use craft\db\Migration;
use craft\helpers\MigrationHelper;
use putyourlightson\snaptcha\records\SnaptchaRecord;

class m210603_120000_add_value_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $table = SnaptchaRecord::tableName();

        // Delete all rows to avoid having stale values in the DB
        $this->delete($table);

        if (!$this->db->columnExists($table, 'value')) {
            $this->addColumn($table, 'value', $this->string()->after('key'));

            $this->createIndex(null, $table, 'value', false);

            MigrationHelper::dropIndexIfExists($table, 'key');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo self::class." cannot be reverted.\n";

        return false;
    }
}
