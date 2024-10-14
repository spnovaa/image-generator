<?php

namespace App\Repositories\Requests;

use App\Enums\Requests\Status;
use App\Exceptions\GeneralDatabaseException;
use App\Models\RequestHistory;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use Throwable;

class Repo
{
    /**
     * @param RequestHistory $request
     * @return RequestHistory
     * @throws GeneralDatabaseException
     */
    public function create(RequestHistory $history): RequestHistory
    {
        try {
            $history->save();
            return $history;
        } catch (Throwable $throwable) {
            throw new GeneralDatabaseException();
        }
    }


    /**
     * @param RequestHistory $history
     * @return RequestHistory
     * @throws GeneralDatabaseException
     */
    public function update(RequestHistory $history): RequestHistory
    {
        try {
            if ($history->getOriginal('id'))
                $history->save();
            else
                throw new InvalidArgumentException('SERVER ERROR!');

            return $history;
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new GeneralDatabaseException();
        }
    }

    /**
     * @param int $id
     * @return RequestHistory|null
     * @throws GeneralDatabaseException
     */
    public function show(int $id): ?RequestHistory
    {
        try {
            return RequestHistory::find($id);
        } catch (Throwable) {
            throw new GeneralDatabaseException();
        }
    }

    /**
     * @return Collection
     * @throws GeneralDatabaseException
     */
    public function index(): Collection
    {
        try {
            return RequestHistory::all();
        } catch (Throwable) {
            throw new GeneralDatabaseException();
        }
    }

    /**
     * @return Collection
     * @throws GeneralDatabaseException
     */
    public function getReadyRecords(): Collection
    {
        try {
        return RequestHistory::where('status', Status::READY)->get();
        } catch (Throwable) {
            throw new GeneralDatabaseException();
        }
    }
}
