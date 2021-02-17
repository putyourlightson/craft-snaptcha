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

/**
 * Install Migration
 */
class Install extends Migration
{
    /**
     * @return boolean
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

            $this->createIndex(null, $table, 'value', false);
            $this->createIndex(null, $table, 'ipAddress', false);

            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        // Create and save default settings
        $settings = Snaptcha::$plugin->settings;
        $settings->salt = StringHelper::randomString(16);
        Craft::$app->plugins->savePluginSettings(Snaptcha::$plugin, $settings->getAttributes());

        return true;
    }

    /**
     * @return boolean
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%snaptcha}}');

        return true;
    }
}
