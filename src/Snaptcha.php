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
use putyourlightson\snaptcha\variables\SnaptchaVariable;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\web\ForbiddenHttpException;


/**
 * Snaptcha Plugin
 *
 * @property SnaptchaService $snaptcha
 * @property SettingsModel $settings
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
     * @var bool
     */
    public $validated = false;

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

        // Return if controller's enableSnaptchaValidation variable exists and is set to false
        if (isset($event->sender->enableSnaptchaValidation) && $event->sender->enableSnaptchaValidation === false) {
            return;
        }

        /** @var Request $request */
        $request = Craft::$app->getRequest();

        // Return if validation is not enabled, if request is for CP or console or live preview,
        // if method is not post, if a set password path or if URI is excluded from validation.
        if (!$this->settings->validationEnabled
            || $request->getIsCpRequest()
            || $request ->getIsConsoleRequest()
            || $request->getIsPreview()
            || $request->getMethod() !== 'POST'
            || $request->getFullPath() == Craft::$app->getConfig()->getGeneral()->getSetPasswordPath()
            || $this->snaptcha->isExcludedControllerAction()
            || $this->snaptcha->isExcludedUri($request->getUrl())
        ) {
            return;
        }

        // TODO: remove in 3.0.0
        // Check `getIsLivePreview()` for plugins that use tokens, such as Campaign
        if ($request->getIsLivePreview()) {
            return;
        }

        $value = $request->getParam($this->settings->fieldName);

        $this->validated = $this->validated || $this->snaptcha->validateField($value);

        if ($this->validated === false) {
            throw new ForbiddenHttpException(Craft::t('snaptcha', $this->settings->errorMessage));
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
    protected function settingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('snaptcha/_settings', [
            'settings' => $this->settings,
            'config' => Craft::$app->getConfig()->getConfigFromFile('snaptcha'),
        ]);
    }
}
