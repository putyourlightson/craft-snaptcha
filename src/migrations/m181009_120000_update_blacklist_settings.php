<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\migrations;

use Craft;
use craft\db\Migration;
use putyourlightson\snaptcha\Snaptcha;

class m181009_120000_update_blacklist_settings extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Resave plugin settings converting blacklist to array of arrays for editable table field
        $settings = Snaptcha::$plugin->settings;

        if (!is_string($settings->blacklist)) {
            return true;
        }

        $blacklistedIps = preg_split('/\R/', $settings->blacklist);
        $settings->blacklist = [];

        foreach ($blacklistedIps as $blacklistedIp) {
            $settings->blacklist[] = [$blacklistedIp];
        }

        Craft::$app->plugins->savePluginSettings(Snaptcha::$plugin, $settings->getAttributes());

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
