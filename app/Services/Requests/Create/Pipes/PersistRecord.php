<?php

namespace App\Services\Requests\Create\Pipes;

use App\Contracts\RequestHistoryRepository;
use App\Data\PipelinePayload;
use App\Enums\RequestStatus;
use App\Models\RequestHistory;
use Closure;

/**
 * Pipeline step: persist the initial RequestHistory record so it gets a
 * primary key, then derive its filename from that key and persist again.
 */
final readonly class PersistRecord
{
    public function __construct(
        private RequestHistoryRepository $repository,
    ) {}

    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $history = $this->repository->save(new RequestHistory([
            'email'  => $payload->history->email,
            'status' => RequestStatus::PENDING->value,
        ]));

        $extension = $payload->upload?->getClientOriginalExtension() ?: 'jpg';
        $history->file_name = "{$history->id}.{$extension}";
        $history = $this->repository->save($history);

        $payload->history = $history;

        return $next($payload);
    }
}
