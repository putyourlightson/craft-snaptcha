<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\controllers;

use Craft;
use craft\helpers\Html;
use craft\web\Controller;
use putyourlightson\snaptcha\models\SnaptchaModel;
use putyourlightson\snaptcha\Snaptcha;
use yii\web\Response;

class FieldController extends Controller
{
    /**
     * @inheritdoc
     */
    protected array|bool|int $allowAnonymous = true;

    /**
     * Returns the field name.
     */
    public function actionGetFieldName(): Response
    {
        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson(Snaptcha::$plugin->settings->fieldName);
        }

        return $this->asRaw(Snaptcha::$plugin->settings->fieldName);
    }

    /**
     * Returns a field value.
     */
    public function actionGetFieldValue(): Response
    {
        $value = Snaptcha::$plugin->snaptcha->getFieldValue(new SnaptchaModel()) ?? '';

        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson($value);
        }

        return $this->asRaw($value);
    }

    /**
     * Returns an input field name and value.
     *
     * If the request accepts JSON then a JSON object is returned:
     *
     *     {name: "snaptcha", value: "oqSCFUGwoUfvcwKEqaJPGTDNjtoDqfzgvxX0"}
     *
     * Otherwise a raw input field is returned:
     *
     *     <input type="hidden" name="snaptcha" value="oqSCFUGwoUfvcwKEqaJPGTDNjtoDqfzgvxX0">
     */
    public function actionGetField(): Response
    {
        $value = Snaptcha::$plugin->snaptcha->getFieldValue(new SnaptchaModel()) ?? '';

        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson([
                'name' => Snaptcha::$plugin->settings->fieldName,
                'value' => $value,
            ]);
        }

        return $this->asRaw(Html::hiddenInput(Snaptcha::$plugin->settings->fieldName, $value));
    }

    /**
     * Validates a field value.
     */
    public function actionValidateFieldValue(): Response
    {
        $value = Craft::$app->getRequest()->getParam('value');

        $validated = Snaptcha::$plugin->snaptcha->validateField($value);

        if ($validated === false) {
            if (Craft::$app->getRequest()->getAcceptsJson()) {
                return $this->asFailure(Craft::t('snaptcha', Snaptcha::$plugin->settings->errorMessage));
            }

            return $this->asRaw(Craft::t('snaptcha', Snaptcha::$plugin->settings->errorMessage));
        }

        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }

        return $this->asRaw('success');
    }
}
