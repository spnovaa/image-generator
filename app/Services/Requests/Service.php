<?php

namespace App\Services\Requests;

use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Models\RequestHistory;
use App\Services\Requests\Create\Service as CreateService;
use App\Services\Requests\Update\Service as UpdateService;
use App\Services\Requests\Read\Service as ReadService;
use Illuminate\Database\Eloquent\Collection;

readonly class Service
{
    public function __construct(
        private CreateService $cs,
        private ReadService   $rs,
        private UpdateService $us
    )
    {
    }

    /**
     * @param RequestHistory $history
     * @return int
     * @throws GeneralDatabaseException
     * @throws RabbitMQException
     */
    public function create(RequestHistory $history): int
    {
        return $this->cs->create($history);
    }

    /**
     * @param int $id
     * @return RequestHistory
     * @throws GeneralDatabaseException
     */
    public function show(int $id): RequestHistory
    {
        return $this->rs->show($id);
    }

    /**
     * @return Collection
     * @throws GeneralDatabaseException
     */
    public function index(): Collection
    {
        return $this->rs->index();
    }

    /**
     * @param RequestHistory $history
     * @return RequestHistory
     * @throws GeneralDatabaseException
     */
    public function update(RequestHistory $history): RequestHistory
    {
        return $this->us->update($history);
    }
}
