<?php

/**
 * Tests whether performing actions is allowed.
 */

use craft\elements\User;
use putyourlightson\snaptcha\Snaptcha;

beforeEach(function() {
    Craft::$app->getUser()->identity = new User();
});

test('User is not allowed when validation is enabled', function() {
    Snaptcha::$plugin->settings->validateUsers = true;

    expect(Snaptcha::$plugin->snaptcha->isUserAllowed())
        ->toBeFalse();
});

test('User is allowed when validation is disabled', function() {
    Snaptcha::$plugin->settings->validateUsers = false;

    expect(Snaptcha::$plugin->snaptcha->isUserAllowed())
        ->toBeTrue();
});

test('User IP is allowed when in the allow list', function() {
    Snaptcha::$plugin->settings->allowList = ['127.0.0.1'];

    expect(Snaptcha::$plugin->snaptcha->isIpAllowed())
        ->toBeTrue();
});

test('User IP is allowed when in a nested array in the allow list', function() {
    Snaptcha::$plugin->settings->allowList = [['127.0.0.1']];

    expect(Snaptcha::$plugin->snaptcha->isIpAllowed())
        ->toBeTrue();
});

test('User IP is not allowed when not in the allow list', function() {
    Snaptcha::$plugin->settings->allowList = [];

    expect(Snaptcha::$plugin->snaptcha->isIpAllowed())
        ->toBeFalse();
});

test('User IP is denied when in the deny list', function() {
    Snaptcha::$plugin->settings->denyList = ['127.0.0.1'];

    expect(Snaptcha::$plugin->snaptcha->isIpDenied())
        ->toBeTrue();
});

test('User IP is denied when in a nested array in the deny list', function() {
    Snaptcha::$plugin->settings->denyList = [['127.0.0.1']];

    expect(Snaptcha::$plugin->snaptcha->isIpDenied())
        ->toBeTrue();
});

test('User IP is not denied when not in the deny list', function() {
    Snaptcha::$plugin->settings->denyList = [];

    expect(Snaptcha::$plugin->snaptcha->isIpDenied())
        ->toBeFalse();
});
