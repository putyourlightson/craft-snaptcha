<p align="center"><img height="180" src="./src/icon.svg"></p>

# Snaptcha Plugin for Craft CMS 3

Snaptcha is an invisible CAPTCHA that automatically validates forms and prevents spam bots from submitting to your [Craft CMS](https://craftcms.com/) site.

Snaptcha (Simple Non-obtrusive Automated Public Turing test to tell Computers and Humans Apart) will validate all POST requests to the front-end of your site, meaning that it will work with any form and any plugin. Note that since this will affect all POST requests, you must add the required template tag before enabling validation (see usage below).

> Upgrades from Snaptcha v1 for Craft CMS 2 purchased through StraightUpCraft are free! To get your license, please send your original purchase receipt to [info@putyourlightson.net](mailto:info@putyourlightson.net).

## License

This plugin requires a commercial license which can be purchased through the Craft Plugin Store. The license fee is $29 plus $9 per subsequent year for updates (optional).

## Requirements

Craft CMS 3.0.0 or later.

## Installation

To install the plugin, search for "Snaptcha" in the Craft Plugin Store, or install manually using composer.

        composer require putyourlightson/craft-snaptcha

## Usage

After installing the plugin, go to the plugin settings page. Snaptcha validation is disabled by default so that you can first add the required template tag to your forms. Once you have done this you can enable Snaptcha validation.

Add the following template tag to every form that submits a POST request to your site. This will output a hidden input field along with some JavaScript code.

    {{ craft.snaptcha.field }}      // Outputs a hidden input field
    
If you are submitting a POST request through AJAX then you can get the field name and value as follows.

    {{ craft.snaptcha.fieldName }}       // Outputs the name of the field
    
    {{ craft.snaptcha.fieldValue }}      // Outputs the value of the field

You can optionally use the `getField` and `getFieldValue` methods to pass in configuration values that will override the default values in the plugin settings.

    {% set config = {expirationTime: 60, minimumSubmitTime: 3} %}
    
    {{ craft.snaptcha.getField(config) }}         // Outputs a hidden input field
    
    {{ craft.snaptcha.getFieldValue(config) }}    // Outputs the value of the field
    
## Settings

### Validation Enabled

With this setting enabled, Snaptcha will validate all forms submitted through POST requests. Ensure that all of your forms that submit via POST requests have the necessary tags in place before enabling this.

### One Time Key

Enabling this will restrict the number of times that a form can be submitted to one time per page refresh. This is a strong security measure and is recommended for low to medium traffic sites. For high traffic sites, disabling this will prevent the database table that the plugin uses from getting too big. 

### Log Rejected 

Whether rejected form submissions should be logged (log will be written to `storage/logs/snaptcha.log`).

### Field Name

The name of the hidden Snaptcha input field.

### Error Message

The error message that will be displayed if Snaptcha identifies a submission as spam.

### Expiration Time

The expiration time for form submissions in minutes.

### Minimum Submit Time

The minimum time for form submission in seconds (increase this to harden spam blocking).

### Excluded URI Patterns

The URI patterns to exclude from validation.

URI patterns use PCRE regular expressions. Below are some common use cases. You can reference the full syntax [here](http://php.net/manual/en/reference.pcre.pattern.syntax.php).

- `.` Matches any character
- `.*` Matches any character 0 or more times
- `.+` Matches any character 1 or more times
- `\d` Matches any four digits
- `\w` Matches any word character
- `entries` Matches anything containing "entries"
- `^entries` Matches anything beginning with "entries"
- `^entries/entry$` Matches exact URI

### Blacklist

IP addresses to blacklist from all form submissions.

### Config Settings

Snaptcha comes with a config file for a multi-environment way to set the plugin settings. To use it, copy the `config.php` to your project’s main `config` directory as `snaptcha.php` and uncomment any settings you wish to change.

![Settings](docs/images/settings-2.1.0.png)

## Disabling Validation

Validation can be disabled by specifying URI patterns to exclude. Adding a property called `$enableSnaptchaValidation` to any controller class and setting it to `false` will disable validation when the actions in that class are called.

    class WebhookController extends Controller
    {
      /**
       * @var bool Disable Snaptcha validation
       */
      public $enableSnaptchaValidation = false;

## Testing Snaptcha

If you want to test or see how Snaptcha works on your site then navigate to one of your forms, open your browser's inspector and delete the input field that Snaptcha inserted. It will usually be inside your form's markup and will have an ID that begins with the prefix in your extension settings (`snaptcha` by default). After deleting the input field, submit the form and the error message from your plugin settings should appear. 

<small>Created by [PutYourLightsOn](https://putyourlightson.com/).</small>
