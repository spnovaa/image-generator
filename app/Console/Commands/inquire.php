<?php

namespace App\Console\Commands;

use App\Contracts\RequestHistoryRepository;
use App\Services\Requests\ImageGenerating\Service as ImageGeneratingService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Polls the database for {@see \App\Enums\RequestStatus::READY} records
 * and runs the image-generation pipeline for each.
 *
 * Scheduled every minute — see routes/console.php.
 */
final class Inquire extends Command
{
    protected $signature   = 'app:inquire';
    protected $description = 'Generate images for all ready conversion requests.';

    public function handle(
        RequestHistoryRepository $repository,
        ImageGeneratingService $service,
    ): int {
        $records = $repository->ready();

        $this->info("Processing {$records->count()} ready record(s).");

        foreach ($records as $record) {
            try {
                $service->handle($record);
                $this->line("  ✓ #{$record->id} done");
            } catch (Throwable $e) {
                $this->error("  ✗ #{$record->id} failed: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
