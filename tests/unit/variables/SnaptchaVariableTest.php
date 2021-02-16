<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\snaptchatests\unit\variables;

use Codeception\Test\Unit;
use putyourlightson\snaptcha\variables\SnaptchaVariable;
use UnitTester;

/**
 * @author    PutYourLightsOn
 * @package   Snaptcha
 * @since     3.0.0
 */

class SnaptchaVariableTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var SnaptchaVariable
     */
    protected $variable;

    protected function _before()
    {
        parent::_before();

        $this->variable = new SnaptchaVariable();
    }

    public function testGetField()
    {
        $field = $this->variable->getField();

        $this->assertStringContainsString(
            'name="'.$this->variable->getFieldName().'" value="" autocomplete="off"',
            $field
        );
    }
}
