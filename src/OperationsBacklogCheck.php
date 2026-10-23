<?php

declare(strict_types=1);

namespace IllumaLaw\HealthCheckOperations;

use Illuminate\Support\Collection;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Throwable;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperationManager;

final class OperationsBacklogCheck extends Check
{
    public function run(): Result
    {
        try {
            /** @var Collection<array-key, \SplFileInfo> $pending */
            $pending = OneTimeOperationManager::getUnprocessedFiles();
            /** @var list<\SplFileInfo> $files */
            $files = $pending->values()->all();

            /** @var list<int> $mtimes */
            $mtimes = array_map(static fn (\SplFileInfo $file): int => (int) $file->getMTime(), $files);
            $names = array_slice(array_map(static fn (\SplFileInfo $file): string => $file->getBasename('.php'), $files), 0, 12);
        } catch (Throwable $exception) {
            return Result::make()
                ->failed(trans('healthcheck-operations::messages.query_failed', ['message' => $exception->getMessage()]))
                ->shortSummary('Check failed');
        }

        $count = count($files);

        if ($count === 0) {
            return Result::make()
                ->meta(['pending_count' => 0])
                ->shortSummary('None')
                ->ok('No pending one-time operations.');
        }

        sort($mtimes);
        $oldestMtime = $mtimes[0] ?? 0;
        $oldestAgeHours = max(0, ((int) now()->timestamp - $oldestMtime) / 3600);
        $result = Result::make()
            ->meta([
                'pending_count' => $count,
                'oldest_pending_hours' => round($oldestAgeHours, 2),
                'sample' => $names,
            ])
            ->shortSummary("{$count} pending");

        if ($oldestAgeHours >= 24) {
            $message = trans('healthcheck-operations::messages.stale', ['hours' => 24]);

            if (filter_var(config('healthcheck-operations.required', false), FILTER_VALIDATE_BOOLEAN)) {
                return $result->failed($message);
            }

            return $result->warning($message);
        }

        return $result->warning('There are pending one-time operations waiting for `operations:process`.');
    }
}
