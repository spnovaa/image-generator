<?php

namespace App\Services\Requests;

use App\Contracts\RequestHistoryRepository;
use App\Data\CreateRequestData;
use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Models\RequestHistory;
use App\Services\Requests\Create\Service as CreateService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Application-service facade for the "conversion request" use cases.
 *
 * Thin coordinator: delegates writes to {@see CreateService} (which runs
 * a pipeline) and reads to the {@see RequestHistoryRepository}. The
 * legacy Read/Update sub-services are gone — they were one-line
 * delegators that added no value.
 */
final readonly class Service
{
    public function __construct(
        private CreateService            $create,
        private RequestHistoryRepository $repository,
    ) {}

    /**
     * @throws GeneralDatabaseException
     * @throws RabbitMQException
     */
    public function create(CreateRequestData $data): RequestHistory
    {
        return $this->create->handle($data);
    }

    /**
     * @throws GeneralDatabaseException
     */
    public function show(int $id): ?RequestHistory
    {
        return $this->repository->find($id);
    }

    /**
     * @throws GeneralDatabaseException
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }
}
