<?php

namespace App\Services\Requests\Captioning;

use App\Data\PipelinePayload;
use App\Enums\RequestStatus;
use App\Models\RequestHistory;
use App\Services\Requests\Captioning\Pipes\DownloadOriginalImage;
use App\Services\Requests\Captioning\Pipes\GenerateCaption;
use App\Services\Requests\Captioning\Pipes\PersistCaption;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Orchestrates the captioning use case as a pipeline.
 */
final class Service
{
    /**
     * @var array<int, class-string>
     */
    private const PIPES = [
        DownloadOriginalImage::class,
        GenerateCaption::class,
        PersistCaption::class,
    ];

    public function __construct(
        private readonly Pipeline $pipeline,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(RequestHistory $history): RequestHistory
    {
        $payload = new PipelinePayload(history: $history);

        try {
            return DB::transaction(function () use ($payload): RequestHistory {
                /** @var PipelinePayload $result */
                $result = $this->pipeline
                    ->send($payload)
                    ->through(self::PIPES)
                    ->thenReturn();

                return $result->history;
            });
        } catch (Throwable $e) {
            $history->markAs(RequestStatus::FAILURE)->save();
            throw $e;
        }
    }
}
