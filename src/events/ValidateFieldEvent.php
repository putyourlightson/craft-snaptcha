<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptcha\events;

use craft\events\CancelableEvent;

/**
 * Validate Field Event
 */
class ValidateFieldEvent extends CancelableEvent
{
    // Properties
    // =========================================================================

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
