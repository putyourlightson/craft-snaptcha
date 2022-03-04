# Static Analysis

To run static analysis on the plugin, install PHPStan and run the following command from the root of your project.

```shell
./vendor/bin/phpstan analyse -c vendor/putyourlightson/craft-snaptcha/phpstan.neon
```

# Testing

To test the plugin, install Codeception, update `.env` and run the following command from the root of your project.

```shell
./vendor/bin/codecept run -c ./vendor/putyourlightson/craft-snaptcha unit
```

Or to run a specific test.

```shell
./vendor/bin/codecept run -c ./vendor/putyourlightson/craft-snaptcha unit variables/SnaptchaVariableTest:getField
```

> Ensure that the database you specify in `.env` is not one that actually contains any data as it will be cleared when the tests are run. 
