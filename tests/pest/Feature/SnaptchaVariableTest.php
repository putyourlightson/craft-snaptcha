<?php

/**
 * Tests the markup generated by the Snaptcha variable.
 */

use putyourlightson\snaptcha\variables\SnaptchaVariable;

test('Field is named after field name setting', function() {
    $variable = new SnaptchaVariable();
    $field = $variable->getField();

    expect((string)$field)
        ->toContain('name="' . $variable->getFieldName() . '" value="" data-key=');
});
