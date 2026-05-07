<?php

namespace App\Services\Requests\Create;

use App\Data\CreateRequestData;
use App\Data\PipelinePayload;
use App\Enums\RequestStatus;
use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Models\RequestHistory;
use App\Services\Requests\Create\Pipes\DispatchCaptioningJob;
use App\Services\Requests\Create\Pipes\PersistRecord;
use App\Services\Requests\Create\Pipes\UploadOriginalImage;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Orchestrates the "create conversion request" use case as a pipeline.
 *
 * Pattern: Pipeline (a.k.a. Chain of Responsibility, the Laravel flavour).
 *  Each step is a single-responsibility class, easy to reorder or replace.
 */
final class Service
{
    /**
     * @var array<int, class-string>
     */
    private const PIPES = [
        PersistRecord::class,
        UploadOriginalImage::class,
        DispatchCaptioningJob::class,
    ];

    public function __construct(
        private readonly Pipeline $pipeline,
    ) {}

    /**
     * @throws GeneralDatabaseException
     * @throws RabbitMQException
     * @throws Throwable
     */
    public function handle(CreateRequestData $data): RequestHistory
    {
        $history = new RequestHistory([
            'email'  => $data->email,
            'status' => RequestStatus::PENDING->value,
        ]);

        $payload = new PipelinePayload(history: $history, upload: $data->image);

        return DB::transaction(function () use ($payload): RequestHistory {
            /** @var PipelinePayload $result */
            $result = $this->pipeline
                ->send($payload)
                ->through(self::PIPES)
                ->thenReturn();

            return $result->history;
        });
    }
}
