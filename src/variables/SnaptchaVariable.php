<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\variables;

use craft\helpers\Html;
use craft\helpers\StringHelper;
use craft\helpers\Template;
use putyourlightson\snaptcha\models\SnaptchaModel;
use putyourlightson\snaptcha\Snaptcha;
use Twig\Markup;

/**
 * Snaptcha Variable
 */
class SnaptchaVariable
{
    /**
     * Returns the field name.
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return Snaptcha::$plugin->settings->fieldName;
    }

    /**
     * Returns a field value.
     *
     * @param array|null $config
     *
     * @return string
     */
    public function getFieldValue($config = null): string
    {
        $model = new SnaptchaModel($config);

        return Snaptcha::$plugin->snaptcha->getFieldValue($model) ?? '';
    }

    /**
     * Returns a field.
     *
     * @param array|null $config
     *
     * @return Markup
     */
    public function getField($config = null): Markup
    {
        $model = new SnaptchaModel($config);
        $key = Snaptcha::$plugin->snaptcha->getFieldKey($model);
        $salt = Snaptcha::$plugin->settings->salt;

        if ($key === '') {
            return Template::raw('<!-- Snaptcha field could not be created. -->');
        }

        $fieldName = $this->getFieldName();
        $fieldId = $fieldName.'-'.StringHelper::randomString(5);

        $field = Html::hiddenInput($fieldName, '', [
            'id' => $fieldId,
            'data-key' => $key,
            'autocomplete' => 'off',
        ]);
        $field .= Html::script('document.getElementById("'.$fieldId.'").value = btoa(document.getElementById("'.$fieldId.'").dataset.key + "'.$salt.'");');

        return Template::raw($field);
    }
}
