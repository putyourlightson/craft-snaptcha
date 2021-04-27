<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha;

use Craft;
use craft\base\Plugin;
use craft\web\Controller;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use putyourlightson\snaptcha\models\SettingsModel;
use putyourlightson\snaptcha\services\SnaptchaService;
use putyourlightson\snaptcha\variables\SnaptchaVariable;
use yii\base\ActionEvent;
use yii\base\Event;

/**
 * @property SnaptchaService $snaptcha
 * @property SettingsModel $settings
 */
class Snaptcha extends Plugin
{
    /**
     * @var Snaptcha
     */
    public static $plugin;

    /**
     * @var bool
     */
    public $validated = false;

    /**
     * @inherit
     */
    public $hasCpSettings = true;

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
     */
    public function validateField(ActionEvent $event)
    {
        if (Craft::$app->getErrorHandler()->exception !== null) {
            return;
        }

        if (isset($event->sender->enableSnaptchaValidation) && $event->sender->enableSnaptchaValidation === false) {
            return;
        }

        $request = Craft::$app->getRequest();

        if (!$this->settings->validationEnabled
            || $request->getIsConsoleRequest()
            || $request->getIsCpRequest()
            || $request->getIsPreview()
            || $request->getMethod() !== 'POST'
            || $request->getFullPath() == Craft::$app->getConfig()->getGeneral()->getSetPasswordPath()
            || $this->snaptcha->isExcludedControllerAction()
        ) {
            return;
        }

        // TODO: remove in 4.0.0
        // Check `getIsLivePreview()` for plugins that use tokens, such as Campaign
        if ($request->getIsLivePreview()) {
            return;
        }

        $value = $request->getParam($this->settings->fieldName);

        $this->validated = $this->validated || $this->snaptcha->validateField($value);

        if ($this->validated === false) {
            $variables = [
                'settings' => $this->settings,
                'postedValues' => $this->snaptcha->getPostedValues(),
            ];

            if ($this->settings->errorTemplate) {
                $output = Craft::$app->view->renderPageTemplate($this->settings->errorTemplate, $variables);
            }
            else {
                Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

                $output = Craft::$app->view->renderPageTemplate('snaptcha/_error', $variables);
            }

            Craft::$app->response->statusCode = 400;
            Craft::$app->response->content = $output;
            Craft::$app->end();
        }
    }

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
    protected function settingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('snaptcha/_settings', [
            'settings' => $this->settings,
            'config' => Craft::$app->getConfig()->getConfigFromFile('snaptcha'),
        ]);
    }
}
