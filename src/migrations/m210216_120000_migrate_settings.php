<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\StringHelper;
use putyourlightson\snaptcha\models\SettingsModel;
use putyourlightson\snaptcha\Snaptcha;

class m210216_120000_migrate_settings extends Migration
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

        // Resave plugin settings
        $settings = Snaptcha::$plugin->settings;
        $settings->salt = StringHelper::randomString(16);

        // Only update if original message is unchanged
        if ($settings->errorMessage == 'Sorry, you have failed the security test. Please ensure that you have javascript enabled and that you refresh the page that you are trying to submit.') {
            $settings->errorMessage = (new SettingsModel())->errorMessage;
        }

        $settings->denyList = $settings->blacklist;
        $settings->blacklist = [];

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
