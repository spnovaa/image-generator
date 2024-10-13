<?php

namespace App\Services\Requests\Read;

use App\Exceptions\GeneralDatabaseException;
use App\Models\RequestHistory;
use App\Repositories\Requests\Repo as RequestHistoryRepo;
use Illuminate\Database\Eloquent\Collection;

class Service
{
    public function __construct(
        private RequestHistoryRepo $repo
    )
    {
    }

    /**
     * @param int $id
     * @return RequestHistory|null
     * @throws GeneralDatabaseException
     */
    public function show(int $id): ?RequestHistory
    {
        return $this->repo->show($id);
    }

    /**
     * @return Collection
     * @throws GeneralDatabaseException
     */
    public function index()
    {
        return $this->repo->index();
    }
}
