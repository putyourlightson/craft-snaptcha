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

        // define the relative path to CSS/JS files that should be registered with the page when this asset bundle is registered
        $this->css = [
            'css/error.css',
        ];

        $this->js = [
            'js/error.js',
        ];

        parent::init();
    }
}
