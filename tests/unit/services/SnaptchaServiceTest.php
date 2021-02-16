<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptchatests\unit\services;

use Codeception\Test\Unit;
use Craft;
use putyourlightson\snaptcha\Snaptcha;
use UnitTester;

/**
 * @author    PutYourLightsOn
 * @package   Snaptcha
 * @since     3.0.0
 */

class SnaptchaServiceTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testIsIpAllowed()
    {
        $ipAddress = Craft::$app->getRequest()->getUserIP();
        Snaptcha::$plugin->settings->allowList = [$ipAddress];

        $this->assertTrue(Snaptcha::$plugin->snaptcha->isIpAllowed());
    }

    public function testIsIpDenied()
    {
        $ipAddress = Craft::$app->getRequest()->getUserIP();
        Snaptcha::$plugin->settings->denyList = [$ipAddress];

        $this->assertTrue(Snaptcha::$plugin->snaptcha->isIpDenied());
    }
}
