<?php

declare(strict_types=1);

namespace IllumaLaw\HealthCheckOperations;

use Illuminate\Support\Collection;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperationManager;

final class OperationsBacklogCheck extends Check
{
    public function run(): Result
    {
        /** @var Collection<array-key, \SplFileInfo> $pending */
        $pending = OneTimeOperationManager::getUnprocessedFiles();
        /** @var list<\SplFileInfo> $files */
        $files = $pending->values()->all();
        $count = count($files);

        if ($count === 0) {
            return Result::make()
                ->meta(['pending_count' => 0])
                ->shortSummary('None')
                ->ok('No pending one-time operations.');
        }

        /** @var list<int> $mtimes */
        $mtimes = array_map(static fn (\SplFileInfo $file): int => (int) $file->getMTime(), $files);
        sort($mtimes);
        $oldestMtime = $mtimes[0] ?? 0;
        $oldestAgeHours = max(0, (now()->timestamp - $oldestMtime) / 3600);
        $names = array_slice(array_map(static fn (\SplFileInfo $file): string => $file->getBasename('.php'), $files), 0, 12);

        $result = Result::make()
            ->meta([
                'pending_count' => $count,
                'oldest_pending_hours' => round($oldestAgeHours, 2),
                'sample' => $names,
            ])
            ->shortSummary("{$count} pending");

        if ($oldestAgeHours >= 24) {
            return $result->failed('Pending one-time operations are older than 24 hours.');
        }

        return $result->warning('There are pending one-time operations waiting for `operations:process`.');
    }
}
