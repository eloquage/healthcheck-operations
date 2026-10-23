<?php

declare(strict_types=1);

use IllumaLaw\HealthCheckOperations\HealthcheckOperationsServiceProvider;
use Illuminate\Support\ServiceProvider;

it('registers the config publish path under the correct tag', function () {
    $provider = app()->getProvider(IllumaLaw\HealthCheckOperations\HealthcheckOperationsServiceProvider::class);
    $source = dirname((new \ReflectionClass($provider))->getFileName()).'/../config/healthcheck-operations.php';

    expect(ServiceProvider::pathsToPublish($provider::class, 'healthcheck-operations-config'))
        ->toBe([$source => config_path('healthcheck-operations.php')]);
});

it('loads config with the correct default', function () {
    expect(config('healthcheck-operations.required'))->toBeFalse();
});
