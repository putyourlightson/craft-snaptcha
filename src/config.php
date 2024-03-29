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
    '*' => [
        // Whether form submissions should be validated. Ensure that all of your forms that submit via POST requests have the necessary tags in place before enabling this.
        //'validationEnabled' => false,

        // Whether form submissions should be validated for logged-in users (recommended if public user registration is enabled).
        //'validateUsers' => true,

        // Whether form submissions should be limited to one time per page refresh (recommended for low to medium traffic sites).
        //'oneTimeKey' => true,

        // Whether rejected form submissions should be logged (log will be written to /Users/ben/Sites/craft3/storage/logs/snaptcha.log).
        //'logRejected' => true,

        // The name of the hidden Snaptcha input field.
        //'fieldName' => 'snaptcha',

        // The title that will be displayed if Snaptcha identifies a submission as spam.
        //'errorTitle' => 'Error',

        // The message that will be displayed if Snaptcha identifies a submission as spam.
        //'errorMessage' => 'For some reason your submission was flagged as spam. Please click the button below to resubmit your data.'

        // The error that will be displayed if Snaptcha identifies a submission as spam and JavaScript is disabled.
        //'errorJavascriptDisabled' => 'JavaScript must be enabled for your submission to be verified. Please enable it and refresh the page.'

        // The text that will be displayed in the button if Snaptcha identifies a submission as spam.
        //'errorButtonText' => 'Click this button to resubmit the form.',

        // The template to use if Snaptcha identifies a submission as spam.
        //'errorTemplate' => '',

        // The minimum time for form submission in seconds (increase this to harden spam blocking).
        //'minimumSubmitTime' => 1,

        // The expiration time for form submissions in minutes.
        //'expirationTime' => 60,

        // The controller actions to exclude from validation.
        //'excludeControllerActions' => [],

        // IP addresses to allow for all form submissions.
        //'allowList' => [],

        // IP addresses to deny for all form submissions.
        //'denyList' => [],
    ],
];
