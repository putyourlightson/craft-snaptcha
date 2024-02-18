<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha;

use Craft;
use craft\base\Plugin;
use craft\log\MonologTarget;
use craft\web\Controller;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LogLevel;
use putyourlightson\snaptcha\models\SettingsModel;
use putyourlightson\snaptcha\services\SnaptchaService;
use putyourlightson\snaptcha\variables\SnaptchaVariable;
use yii\base\ActionEvent;
use yii\base\Controller as BaseController;
use yii\base\Event;
use yii\log\Logger;

/**
 * @property SnaptchaService $snaptcha
 * @property SettingsModel $settings
 */
class Snaptcha extends Plugin
{
    /**
     * @var Snaptcha
     */
    public static Snaptcha $plugin;

    /**
     * @inheritdoc
     */
    public static function config(): array
    {
        return [
            'components' => [
                'snaptcha' => ['class' => SnaptchaService::class],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public string $schemaVersion = '3.0.6';

    /**
     * @inheritdoc
     */
    public string $minVersionRequired = '3.0.6';

    /**
     * @var bool Whether the current request has already been validated.
     */
    private bool $validated = false;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        $this->registerVariables();
        $this->registerLogTarget();
        $this->registerActionEvents();
    }

    /**
     * Logs a message.
     */
    public function log(string $message, array $params = [], int $type = Logger::LEVEL_INFO): void
    {
        $message = Craft::t('snaptcha', $message, $params);

        Craft::getLogger()->log($message, $type, 'snaptcha');
    }

    /**
     * Registers variables.
     */
    private function registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT,
            function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('snaptcha', SnaptchaVariable::class);
            }
        );
    }

    /**
     * Registers a custom log target, keeping the format as simple as possible.
     *
     * @see LineFormatter::SIMPLE_FORMAT
     */
    private function registerLogTarget(): void
    {
        Craft::getLogger()->dispatcher->targets[] = new MonologTarget([
            'name' => 'snaptcha',
            'categories' => ['snaptcha'],
            'level' => LogLevel::INFO,
            'logContext' => false,
            'allowLineBreaks' => false,
            'formatter' => new LineFormatter(
                format: "[%datetime%] %message%\n",
                dateFormat: 'Y-m-d H:i:s',
            ),
        ]);
    }

    /**
     * Registers action events.
     */
    private function registerActionEvents(): void
    {
        // Register action event
        Event::on(Controller::class, BaseController::EVENT_BEFORE_ACTION,
            function(ActionEvent $event) {
                $this->validateField($event);
            }
        );
    }

    /**
     * Validates a submitted field.
     */
    public function validateField(ActionEvent $event): void
    {
        // Only validate once.
        if ($this->validated === true) {
            return;
        }

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
            || $request->getIsGraphql()
            || $request->getIsPreview()
            || $request->getMethod() !== 'POST'
            || $request->getFullPath() == Craft::$app->getConfig()->getGeneral()->getSetPasswordPath()
            || $this->snaptcha->isExcludedControllerAction($event->action)
        ) {
            return;
        }

        // Check `getIsLivePreview()` for plugins that use tokens
        if ($request->getIsLivePreview()) {
            return;
        }

        $value = $request->getParam($this->settings->fieldName);

        if (!$this->snaptcha->validateField($value, $event->action)) {
            $variables = [
                'settings' => $this->settings,
                'postedValues' => $this->snaptcha->getPostedValues(),
            ];

            if ($this->settings->errorTemplate) {
                $output = Craft::$app->view->renderPageTemplate($this->settings->errorTemplate, $variables);
            } else {
                Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

                $output = Craft::$app->view->renderPageTemplate('snaptcha/_error', $variables);
            }

            Craft::$app->response->statusCode = 400;
            Craft::$app->response->content = $output;
            Craft::$app->end();
        }

        $this->validated = true;
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
    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('snaptcha/_settings', [
            'settings' => $this->settings,
            'config' => Craft::$app->getConfig()->getConfigFromFile('snaptcha'),
        ]);
    }
}
