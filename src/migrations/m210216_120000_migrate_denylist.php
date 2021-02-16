<?php

namespace putyourlightson\snaptcha\migrations;

use Craft;
use craft\db\Migration;
use putyourlightson\snaptcha\Snaptcha;

class m210216_120000_migrate_denylist extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $schemaVersion = Craft::$app->projectConfig
            ->get('plugins.snaptcha.schemaVersion', true);

        if (!version_compare($schemaVersion, '3.0.0', '<')) {
            return true;
        }

        // Resave plugin settings
        $settings = Snaptcha::$plugin->settings;
        $settings->denyList = $settings->blacklist;
        $settings->blacklist = [];

        Craft::$app->plugins->savePluginSettings(Snaptcha::$plugin, $settings->getAttributes());
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
