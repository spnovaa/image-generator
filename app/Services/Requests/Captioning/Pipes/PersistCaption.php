<?php

namespace App\Services\Requests\Captioning\Pipes;

use App\Contracts\RequestHistoryRepository;
use App\Data\PipelinePayload;
use App\Exceptions\GeneralDatabaseException;
use Closure;

final readonly class PersistCaption
{
    public function __construct(
        private RequestHistoryRepository $repository,
    ) {}

    /**
     * @throws GeneralDatabaseException
     */
    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $payload->history = $this->repository->save($payload->history);

        return $next($payload);
    }
}
