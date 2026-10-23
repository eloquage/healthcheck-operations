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
    File::put($this->opsDir.'/2023_01_01_000000_op.php', '<?php ');

    $result = OperationsBacklogCheck::new()->run();

    expect($result->status)->toEqual(Status::warning())
        ->and($result->shortSummary)->toBe('1 pending');
});

it('fails when there are old pending operations', function () {
    $path = $this->opsDir.'/2023_01_01_000000_old_op.php';
    $path = $this->opsDir.'/2023_01_01_000000_old_op.php';
    File::put($path, '<?php ');

    touch($path, (int) now()->subHours(25)->timestamp);

    $result = OperationsBacklogCheck::new()->run();

    expect($result->status)->toEqual(Status::failed())
        ->and($result->shortSummary)->toBe('1 pending');
});
