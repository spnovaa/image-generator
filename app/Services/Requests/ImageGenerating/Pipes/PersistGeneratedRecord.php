<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Contracts\RequestHistoryRepository;
use App\Data\PipelinePayload;
use App\Enums\RequestStatus;
use App\Exceptions\GeneralDatabaseException;
use Closure;

final readonly class PersistGeneratedRecord
{
    public function __construct(
        private RequestHistoryRepository $repository,
    ) {}

    /**
     * @throws GeneralDatabaseException
     */
    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $payload->history->markAs(RequestStatus::DONE);
        $payload->history = $this->repository->save($payload->history);

        return $next($payload);
    }
}
