<?php

namespace App\Services\Requests\ImageGenerating;

use App\Data\PipelinePayload;
use App\Enums\RequestStatus;
use App\Models\RequestHistory;
use App\Services\Requests\ImageGenerating\Pipes\AnnounceImageGenerated;
use App\Services\Requests\ImageGenerating\Pipes\GenerateImage;
use App\Services\Requests\ImageGenerating\Pipes\PersistGeneratedRecord;
use App\Services\Requests\ImageGenerating\Pipes\UploadGeneratedImage;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Orchestrates image-generation as a pipeline. Side-effects (e-mail,
 * webhooks, analytics) are decoupled via the {@see \App\Events\ImageGenerated}
 * domain event dispatched in {@see AnnounceImageGenerated}.
 */
final class Service
{
    /**
     * @var array<int, class-string>
     */
    private const PIPES = [
        GenerateImage::class,
        UploadGeneratedImage::class,
        PersistGeneratedRecord::class,
        AnnounceImageGenerated::class,
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
