# Release Notes for Snaptcha

## 5.2.0 - 2026-02-04

- The `auth` and `users` controllers are now excluded from validation, except for `users/save-user` ([#34](https://github.com/putyourlightson/craft-snaptcha/issues/34)).

## 5.1.2 - 2025-07-01

- Improved the check for nested uploaded file fields.

## 5.1.1 - 2025-06-23

- Improved the check for uploaded files.

## 5.1.0 - 2025-06-23

- Added an error message that is displayed if Snaptcha identifies a submission as spam when a file is uploaded, in which case the user must go back and resubmit their data ([#32](https://github.com/putyourlightson/craft-snaptcha/issues/32)).
- Added the ability to maintain the encoding type of the submitted form on the error page.

## 5.0.0 - 2024-04-08

- Added compatibility with Craft 5.
