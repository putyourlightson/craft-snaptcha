<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset as CraftCpAsset;

class CpAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = '@putyourlightson/snaptcha/resources';

        $this->depends = [
            CraftCpAsset::class,
        ];

        $this->css = [
            'css/cp.css',
        ];

        parent::init();
    }
}
