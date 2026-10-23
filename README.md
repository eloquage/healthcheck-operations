# Healthcheck operations for Laravel

[![Tests](https://github.com/eloquage/healthcheck-operations/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/healthcheck-operations/actions)
[![Packagist License](https://img.shields.io/badge/Licence-MIT-blue)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-operations?label=Version)](https://packagist.org/packages/illuma-law/healthcheck-operations)

A focused operations backlog health check for Spatie's [Laravel Health](https://spatie.be/docs/laravel-health/v1/introduction) package.

This package provides a simple, direct health check to verify that your [Laravel One-Time Operations](https://github.com/TimoKoerber/laravel-one-time-operations) are being processed and don't remain pending for too long.

## Features

- **Pending Operations Detection:** Automatically detects if there are any pending one-time operations waiting to be processed.
- **Aging Backlog Check:** Monitors the age of the oldest pending operation. If operations are older than 24 hours, the check will degrade to a Failed state.
- **Detailed Meta:** Reports the count of pending operations, the age of the oldest one, and a sample of operation filenames.

## Installation

Require this package with composer:

```shell
composer require illuma-law/healthcheck-operations
```

## Usage & Integration

Register the check inside your application's health service provider (e.g. `AppServiceProvider` or a dedicated `HealthServiceProvider`), alongside your other Spatie Laravel Health checks:

### Basic Registration

```php
use IllumaLaw\HealthCheckOperations\OperationsBacklogCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    OperationsBacklogCheck::new(),
]);
```

### Expected Result States

The check interacts with the Spatie Health dashboard and JSON endpoints using these states:

- **Ok:** There are no pending one-time operations.
- **Warning:** There are pending operations, but they are relatively new (under 24 hours).
- **Failed:** There are pending operations that have been sitting unprocessed for more than 24 hours.

## Testing

Run the test suite:

```shell
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
