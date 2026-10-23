<?php

declare(strict_types=1);

use IllumaLaw\HealthCheckOperations\OperationsBacklogCheck;
use Illuminate\Support\Facades\File;
use Spatie\Health\Enums\Status;

beforeEach(function () {
    $this->opsDir = database_path('operations');
    File::makeDirectory($this->opsDir, 0755, true, true);
    config()->set('one-time-operations.directory', 'database/operations');
});

afterEach(function () {
    File::deleteDirectory($this->opsDir);
});

it('succeeds when there are no pending operations', function () {
    $result = OperationsBacklogCheck::new()->run();

    expect($result->status)->toEqual(Status::ok())
        ->and($result->shortSummary)->toBe('None');
});

it('warns when there are recent pending operations', function () {
    File::put($this->opsDir.'/2023_01_01_000000_op.php', '<?php ');

    $result = OperationsBacklogCheck::new()->run();

    expect($result->status)->toEqual(Status::warning())
        ->and($result->shortSummary)->toBe('1 pending');
});

it('warns when old pending operations are not required', function () {
    $path = $this->opsDir.'/2023_01_01_000000_old_op.php';
    File::put($path, '<?php ');

    touch($path, (int) now()->subHours(25)->timestamp);

    $result = OperationsBacklogCheck::new()->run();

    expect($result->status)->toEqual(Status::warning())
        ->and($result->shortSummary)->toBe('1 pending');
});

it('fails when old pending operations are required', function () {
    config()->set('healthcheck-operations.required', true);

    $path = $this->opsDir.'/2023_01_01_000000_old_op.php';
    File::put($path, '<?php ');
    touch($path, (int) now()->subHours(25)->timestamp);

    $result = OperationsBacklogCheck::new()->run();

    expect($result->status)->toEqual(Status::failed())
        ->and($result->shortSummary)->toBe('1 pending');
});

it('fails when it cannot read the operation backlog', function () {
    File::put($this->opsDir.'/2023_01_01_000000_op.php', '<?php ');
    config()->set('one-time-operations.table', 'missing_operations_table');

    $result = OperationsBacklogCheck::new()->run();

    expect($result->status)->toEqual(Status::failed())
        ->and($result->shortSummary)->toBe('Check failed')
        ->and($result->notificationMessage)->toContain('Could not check pending one-time operations');
});
