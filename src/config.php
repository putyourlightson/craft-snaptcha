<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

/**
 * Snaptcha config.php
 *
 * This file exists only as a template for the Snaptcha settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'snaptcha.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
    // Whether form submissions should be validated. Ensure that all of your forms that submit via POST requests have the necessary tags in place before enabling this.
    //'validationEnabled' => false,

    // Whether form submissions should be limited to one time per page refresh (recommended for low to medium traffic sites).
    //'oneTimeKey' => true,

    // Whether rejected form submissions should be logged (log will be written to /Users/ben/Sites/craft3/storage/logs/snaptcha.log).
    //'logRejected' => true,

    // The name of the hidden Snaptcha input field.
    //'fieldName' => 'snaptcha',

    // The error message that will be displayed if Snaptcha identifies a submission as spam.
    //'errorMessage' => 'Sorry, you have failed the security test. Please ensure that you have javascript enabled and that, you refresh the page that you are trying to submit.'

    // The expiration time for form submissions in minutes.
    //'expirationTime' => 60,

    // The minimum time for form submission in seconds (increase this to harden spam blocking).
    //'minimumSubmitTime' => 3,

    // The URI patterns to exclude from validation.
    //'excludedUriPatterns' => [],

    // IP addresses to blacklist from all form submissions.
    //'blacklist' => [],
];
