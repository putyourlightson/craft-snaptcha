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
    public array $excludeControllerActions = [];

    /**
     * @var string|null
     */
    public ?string $value = null;

    /**
     * @var Action|null
     */
    public ?Action $action = null;

    /**
     * @var bool
     */
    public bool $skipValidation = false;
}
