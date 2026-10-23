# Healthcheck Operations for Laravel

![Healthcheck Operations banner](art/banner.png)

[![Tests](https://github.com/eloquage/healthcheck-operations/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/healthcheck-operations/actions/workflows/run-tests.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-operations?label=Version)](https://packagist.org/packages/illuma-law/healthcheck-operations)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE.md)

A [Spatie Laravel Health](https://spatie.be/docs/laravel-health) check for pending [Laravel One-Time Operations](https://github.com/TimoKoerber/laravel-one-time-operations). It reports the queue size, oldest operation age, and a sample of filenames.

## Requirements

- PHP 8.3 or later
- Laravel 12 or 13
- `spatie/laravel-health` 1.39 or later
- `timokoerber/laravel-one-time-operations` 1.x

## Installation

```bash
composer require illuma-law/healthcheck-operations
```

Laravel discovers the service provider automatically. Publish the configuration to choose whether an old backlog should fail:

```bash
php artisan vendor:publish --tag=healthcheck-operations-config
```

Pending operations warn by default. Set `HEALTH_OPERATIONS_REQUIRED=true` to fail when the oldest operation has been waiting for at least 24 hours. If the package cannot read the operation backlog, the check fails.

## Usage

Register the check in your health service provider:

```php
use IllumaLaw\HealthCheckOperations\OperationsBacklogCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    OperationsBacklogCheck::new(),
]);
```

No pending operations returns **OK**. Pending operations return **Warning** by default; a backlog at least 24 hours old returns **Failed** when the required setting is enabled.

## Testing

```bash
composer test
composer analyse
vendor/bin/pint --test
```

## License

MIT. See [LICENSE.md](LICENSE.md).
