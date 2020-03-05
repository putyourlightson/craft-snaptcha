<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\controllers;

use Craft;
use craft\web\Controller;
use putyourlightson\snaptcha\models\SnaptchaModel;
use putyourlightson\snaptcha\Snaptcha;
use yii\web\Response;

class FieldController extends Controller
{
    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected $allowAnonymous = true;

    // Public Methods
    // =========================================================================

    /**
     * Returns the field name.
     *
     * @return Response
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
     *
     * @return Response
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
     *
     * @return Response
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

        return $this->asRaw('<input type="hidden" name="'.Snaptcha::$plugin->settings->fieldName.'" value="'.$value.'">');
    }

    /**
     * Validates a field value.
     *
     * @return Response
     */
    public function actionValidateFieldValue(): Response
    {
        $value = Craft::$app->getRequest()->getParam('value');

        $validated = Snaptcha::$plugin->snaptcha->validateField($value);

        if ($validated === false) {
            if (Craft::$app->getRequest()->getAcceptsJson()) {
                return $this->asErrorJson(Craft::t('snaptcha', Snaptcha::$plugin->settings->errorMessage));
            }

            return $this->asRaw(Craft::t('snaptcha', Snaptcha::$plugin->settings->errorMessage));
        }

        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }

        return $this->asRaw('success');
    }
}
