<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\migrations;

use Craft;
use craft\db\Migration;
use putyourlightson\snaptcha\records\SnaptchaRecord;

/**
 * Install Migration
 */
class Install extends Migration
{
    // Public Methods
    // =========================================================================

    /**
     * @return boolean
     */
    public function safeUp(): bool
    {
        $snaptchaTable = SnaptchaRecord::tableName();

        if (!$this->db->tableExists($snaptchaTable)) {
            $this->createTable($snaptchaTable, [
                'id' => $this->primaryKey(),
                'key' => $this->string(),
                'ipAddress' => $this->string(),
                'timestamp' => $this->integer(),
                'expirationTime' => $this->integer(),
                'minimumSubmitTime' => $this->integer(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            $this->createIndex(null, $snaptchaTable, 'key', false);
            $this->createIndex(null, $snaptchaTable, 'ipAddress', false);

            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    /**
     * @return boolean
     * @throws \Throwable
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%snaptcha}}');

        return true;
    }
}
