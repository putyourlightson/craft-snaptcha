<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\assets;

use craft\web\AssetBundle;

class ErrorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@putyourlightson/snaptcha/resources';

        $this->css = [
            'css/error.css',
        ];

        $this->js = [
            'js/error.js',
        ];

        parent::init();
    }
}
