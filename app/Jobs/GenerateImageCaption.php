<?php

namespace App\Jobs;

use App\Contracts\RequestHistoryRepository;
use App\Enums\RequestStatus;
use App\Services\Requests\Captioning\Service as CaptioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Asynchronously generates a caption for the image associated with the
 * given {@see \App\Models\RequestHistory} id.
 *
 * Dependencies are resolved through Laravel's container at `handle()`
 * time — the constructor only carries serialisable scalars so the job
 * is safe to enqueue and round-trip through the broker.
 */
final class GenerateImageCaption implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $historyId,
    ) {}

    public function handle(
        RequestHistoryRepository $repository,
        CaptioningService $captioning,
    ): void {
        $history = $repository->find($this->historyId);

        if ($history === null) {
            return; // record was deleted before the job ran
        }

        try {
            $captioning->handle($history);
        } catch (Throwable $e) {
            $history->markAs(RequestStatus::FAILURE)->save();
            throw $e;
        }
    }
}
