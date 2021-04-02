<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptchatests\unit\services;

use Codeception\Test\Unit;
use Craft;
use craft\elements\User;
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

    public function testIsUserAllowed()
    {
        Craft::$app->getUser()->identity = new User();

        $this->assertFalse(Snaptcha::$plugin->snaptcha->isUserAllowed());

        Snaptcha::$plugin->settings->validateUsers = false;
        $this->assertTrue(Snaptcha::$plugin->snaptcha->isUserAllowed());
    }

    public function testIsIpAllowed()
    {
        $ipAddress = Craft::$app->getRequest()->getUserIP();

        Snaptcha::$plugin->settings->allowList = [$ipAddress];
        $this->assertTrue(Snaptcha::$plugin->snaptcha->isIpAllowed());

        Snaptcha::$plugin->settings->allowList = [[$ipAddress]];
        $this->assertTrue(Snaptcha::$plugin->snaptcha->isIpAllowed());
    }

    public function testIsIpDenied()
    {
        $ipAddress = Craft::$app->getRequest()->getUserIP();

        Snaptcha::$plugin->settings->denyList = [$ipAddress];
        $this->assertTrue(Snaptcha::$plugin->snaptcha->isIpDenied());

        Snaptcha::$plugin->settings->denyList = [[$ipAddress]];
        $this->assertTrue(Snaptcha::$plugin->snaptcha->isIpDenied());
    }
}
