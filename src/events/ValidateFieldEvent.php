<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\events;

use craft\events\CancelableEvent;

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
     * @var bool
     */
    public $skipValidation = false;
}
