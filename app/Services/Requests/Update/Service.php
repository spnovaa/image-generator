<?php

namespace App\Services\Requests\Update;

use App\Exceptions\GeneralDatabaseException;
use App\Models\RequestHistory;
use App\Services\Requests\Service as RequestService;

readonly class Service
{
    public function __construct(
        private RequestService $service
    )
    {
    }

    /**
     * for now, the business is simple and does not need any pipes to run.
     * even, using this service instead of the repo directly, might seem redundant to you
     * but please note that these kind of services are to become more complex, and
     * we ought to prepare an appropriate structure, so that, the further developments become
     * much easier.
     *
     * @param RequestHistory $history
     * @return RequestHistory
     * @throws GeneralDatabaseException
     */
    public function update(RequestHistory $history)
    {
        // no need to pipeline, for now.
        return $this->service->update($history);
    }
}
