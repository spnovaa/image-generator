<?php

namespace App\Jobs;

use App\Models\RequestHistory;
use App\Services\Requests\Service as HistoryService;
use App\Services\Requests\Captioning\Service as CaptioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateImageCaption implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int               $history_id,
        private HistoryService    $hs,
        private CaptioningService $cs
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $history = $this->hs->show($this->history_id);
        $this->cs->create($history);
    }
}
