<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\variables;

use craft\helpers\Template;
use putyourlightson\campaign\helpers\StringHelper;
use putyourlightson\snaptcha\models\SettingsModel;
use putyourlightson\snaptcha\models\SnaptchaModel;
use putyourlightson\snaptcha\Snaptcha;
use \Twig_Markup;

/**
 * Snaptcha Variable
 */
class SnaptchaVariable
{
    /**
     * Returns a field
     *
     * @param array|null $config
     *
     * @return Twig_Markup
     */
    public function getField($config = null): Twig_Markup
    {
        $model = new SnaptchaModel($config);

        $value = Snaptcha::$plugin->snaptcha->getFieldValue($model);

        if ($value === null) {
            return Template::raw('<!-- Snaptcha field could not be created. -->');
        }

        /** @var SettingsModel $settings */
        $settings = Snaptcha::$plugin->getSettings();
        $fieldId = $settings->fieldName.'-'.StringHelper::randomString(5);

        $field = '<input type="hidden" id="'.$fieldId.'" name="'.$settings->fieldName.'" value="">';
        $field .= '<script type="text/javascript">document.getElementById("'.$fieldId.'").value = "'.$value.'";</script>';

        return Template::raw($field);
    }
}
