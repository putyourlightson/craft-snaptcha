<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha;

use Craft;
use craft\base\Plugin;
use craft\web\Controller;
use craft\web\Request;
use craft\web\twig\variables\CraftVariable;
use putyourlightson\snaptcha\models\SettingsModel;
use putyourlightson\snaptcha\services\SnaptchaService;
use putyourlightson\snaptcha\services\TestsService;
use putyourlightson\snaptcha\variables\SnaptchaVariable;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\web\ForbiddenHttpException;


/**
 * Snaptcha Plugin
 *
 * @property  SnaptchaService $snaptcha
 */
class Snaptcha extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Snaptcha
     */
    public static $plugin;

    // Properties
    // =========================================================================

    /**
     * @inherit
     */
    public $hasCpSettings = true;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        // Register services as components
        $this->setComponents([
            'snaptcha' => SnaptchaService::class,
        ]);

        // Register variable
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('snaptcha', SnaptchaVariable::class);
        });

        // Register action event
        Event::on(Controller::class, Controller::EVENT_BEFORE_ACTION, function(ActionEvent $event) {
            $this->validateField($event);
        });
    }

    /**
     * Validates a submitted field
     *
     * @param ActionEvent $event
     *
     * @throws ForbiddenHttpException
     */
    public function validateField(ActionEvent $event)
    {
        // Return if this is an exception
        if (Craft::$app->getErrorHandler()->exception !== null) {
            return;
        }

        /** @var Request $request */
        $request = Craft::$app->getRequest();

        if ($request->getIsCpRequest() || $request->getIsLivePreview()) {
            return;
        }

        /** @var SettingsModel $settings */
        $settings = $this->getSettings();

        // Get request method
        $method = $request->getMethod();

        // Only validate if enabled and on post method
        if (!$settings->validationEnabled || $method !== 'POST') {
            return;
        }

        $value = $request->getParam($settings->fieldName);

        $valid = $this->snaptcha->validateField($value);

        if ($valid === false) {
            throw new ForbiddenHttpException(Craft::t('snaptcha', $settings->errorMessage));
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): SettingsModel
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('snaptcha/_settings', [
            'settings' => $this->getSettings(),
            'config' => Craft::$app->getConfig()->getConfigFromFile('snaptcha'),
        ]);
    }
}
