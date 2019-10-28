<?php

namespace putyourlightson\snaptcha\migrations;

use Craft;
use craft\db\Migration;
use putyourlightson\snaptcha\models\SettingsModel;
use putyourlightson\snaptcha\Snaptcha;

class m181009_120000_update_blacklist_settings extends Migration
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Resave plugin settings converting blacklist to array of arrays for editable table field
        $settings = Snaptcha::$plugin->settings;

        if (!is_string($settings->blacklist)) {
            return;
        }

        $blacklistedIps = preg_split('/\R/', $settings->blacklist);
        $settings->blacklist = [];

        foreach ($blacklistedIps as $blacklistedIp) {
            $settings->blacklist[] = [$blacklistedIp];
        }

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
