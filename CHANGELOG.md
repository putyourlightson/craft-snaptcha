# Changelog

## 3.0.5 - Unreleased
### Fixed
- Fixed a bug in which config changes could be made twice if the plugin was installed via project config ([#15](https://github.com/putyourlightson/craft-snaptcha/issues/15)).

## 3.0.4 - 2021-05-06
### Changed
- Excluded requests to the GraphQL API from being validated ([#17](https://github.com/putyourlightson/craft-snaptcha/issues/17)).

## 3.0.3 - 2021-04-27
### Fixed
- Fixed a bug in which the PHP script was allowed to continue even if a submission was flagged as spam ([#16](https://github.com/putyourlightson/craft-snaptcha/issues/16)).

## 3.0.2 - 2021-04-16
### Changed
- The keys of posted arrays are now preserved when a submission is flagged as spam.

## 3.0.1 - 2021-04-14
### Fixed
- Fixed a bug in which an error could be thrown if values were passed in as an array and a submission was flagged as spam.

## 3.0.0 - 2021-04-13
> {warning} The “Excluded URI Patterns” setting has been removed. Use the new “Exclude Controller Actions” setting instead. [Read more](https://putyourlightson.com/articles/snaptcha-3-released)

> {tip} The error screen has been updated. If you previously set a custom error message then you should review the plugin settings.

### Added
- Added a customisable template that allows users to resubmit their data if a submission is flagged as spam.
- Added a “Validate Users” setting that lets you disable validation for logged-in users.
- Added an “Exclude Controller Actions” setting that lets you disable validation for specific controller actions.
- Added an “Allow List” of IP addresses that will not be validated.
- Added unit tests.

### Changed
- Changed the minimum requirement of Craft to version 3.2.1.
- Renamed “Blacklist” to “Deny List”.
- The hidden input field now has an `autocomplete` attribute set to `off`.
- IP addresses are now stored as anonymous hashed values.

### Removed
- Removed the “Excluded URI Patterns” setting. Use the new “Exclude Controller Actions” setting instead.

## 2.3.1 - 2020-12-21
### Changed
- Action requests to the [Complete Cookie Consent plugin](https://plugins.craftcms.com/complete-cookie-consent) controller are excluded from validation ([#12](https://github.com/putyourlightson/craft-snaptcha/issues/12)).

## 2.3.0 - 2020-11-09
### Added
- Added a `beforeExcludeControllerActions` event with the ability to modify controller actions to exclude from validation.

### Changed
- Action requests to the [Commerce plugin](https://plugins.craftcms.com/commerce) webhooks controller are excluded from validation ([#11](https://github.com/putyourlightson/craft-snaptcha/issues/11)).
- Action requests to the [Ellera Cookie Consent plugin](https://plugins.craftcms.com/cookie-consent) controller are excluded from validation ([#12](https://github.com/putyourlightson/craft-snaptcha/issues/12)).
- Improved the excluded URI pattern matching logic.

## 2.2.5 - 2020-03-10
### Fixed
- Fixed validation being applied to live preview using a token query string.

## 2.2.4 - 2020-03-05
### Changed
- Absolute URL is now added to rejected message in log file ([#7](https://github.com/putyourlightson/craft-snaptcha/issues/7)).

## 2.2.3 - 2020-01-25
### Fixed
- Fixed validation being unnecessarily applied to the set password path.

## 2.2.2 - 2020-01-23
### Changed
- Changed `actionGetField` controller action to return a raw input field if the request does not accept JSON.

## 2.2.1 - 2019-12-19
### Fixed
- Fixed validate field value action to work with POSTed values.

## 2.2.0 - 2019-10-28
### Added
- Added field controller actions for better AJAX support.

## 2.1.1 - 2019-02-12
### Changed
- Changed script tag to avoid validation error.

### Fixed
- Fixed possible error when updating plugin.

## 2.1.0 - 2018-10-09
### Added
- Added excluded URI patterns to settings.
- Added table field for blacklisted IP addresses in settings.
- Added check for `enableSnaptchaValidation` parameter in action's controller class.

## 2.0.3 - 2018-10-08
### Fixed
- Fixed bug that was causing issues with live preview.

## 2.0.2 - 2018-10-07
### Fixed
- Fixed bug that was preventing the correct error message from being displayed.

## 2.0.1 - 2018-10-05
### Fixed
- Fixed bug with variable using incorrect StringHelper class.

## 2.0.0 - 2018-10-04
- Initial release.
