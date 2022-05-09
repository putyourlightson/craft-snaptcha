<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\StringHelper;
use putyourlightson\snaptcha\records\SnaptchaRecord;
use putyourlightson\snaptcha\Snaptcha;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $table = SnaptchaRecord::tableName();

        if (!$this->db->tableExists($table)) {
            $this->createTable($table, [
                'id' => $this->primaryKey(),
                'key' => $this->string(),
                'value' => $this->string(),
                'ipAddress' => $this->string(),
                'timestamp' => $this->integer(),
                'expirationTime' => $this->integer(),
                'minimumSubmitTime' => $this->integer(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            $this->createIndex(null, $table, 'value');
            $this->createIndex(null, $table, 'ipAddress');

            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        // Don't make the same config changes twice
        $schemaVersion = Craft::$app->projectConfig->get('plugins.snaptcha.schemaVersion', true);

        if ($schemaVersion !== null) {
            return true;
        }

        // Create and save default settings
        $settings = Snaptcha::$plugin->settings;
        $settings->salt = StringHelper::randomString(16);
        Craft::$app->plugins->savePluginSettings(Snaptcha::$plugin, $settings->getAttributes());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists(SnaptchaRecord::tableName());

        return true;
    }
}
