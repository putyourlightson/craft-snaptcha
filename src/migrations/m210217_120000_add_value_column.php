<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;
use putyourlightson\snaptcha\records\SnaptchaRecord;

class m210217_120000_add_value_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $schemaVersion = Craft::$app->projectConfig
            ->get('plugins.snaptcha.schemaVersion', true);

        if (!version_compare($schemaVersion, '3.0.0', '<')) {
            return true;
        }

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
        echo "m181009_120000_update_blacklist_settings cannot be reverted.\n";

        return false;
    }
}
