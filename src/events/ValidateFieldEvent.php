<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\events;

use craft\events\CancelableEvent;
use yii\base\Action;

class ValidateFieldEvent extends CancelableEvent
{
    /**
     * @var array
     */
    public $excludeControllerActions = [];

    /**
     * @var string|null
     */
    public $value;

    /**
     * @var Action|null
     */
    public $action;

    /**
     * @var bool
     */
    public $skipValidation = false;
}
