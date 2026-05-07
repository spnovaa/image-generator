<?php

namespace App\Repositories;

use App\Contracts\RequestHistoryRepository;
use App\Enums\RequestStatus;
use App\Exceptions\GeneralDatabaseException;
use App\Models\RequestHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Eloquent-backed implementation of {@see RequestHistoryRepository}.
 *
 * All Eloquent exceptions are wrapped as {@see GeneralDatabaseException}
 * so domain code can catch a single, framework-agnostic exception type.
 */
final class EloquentRequestHistoryRepository implements RequestHistoryRepository
{
    public function save(RequestHistory $history): RequestHistory
    {
        try {
            $history->save();
            return $history->refresh();
        } catch (Throwable $e) {
            throw new GeneralDatabaseException(previous: $e);
        }
    }

    public function find(int $id): ?RequestHistory
    {
        try {
            return RequestHistory::query()->find($id);
        } catch (Throwable $e) {
            throw new GeneralDatabaseException(previous: $e);
        }
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return RequestHistory::query()
                ->orderByDesc('id')
                ->paginate($perPage);
        } catch (Throwable $e) {
            throw new GeneralDatabaseException(previous: $e);
        }
    }

    public function ready(): Collection
    {
        try {
            return RequestHistory::query()
                ->where('status', RequestStatus::READY->value)
                ->get();
        } catch (Throwable $e) {
            throw new GeneralDatabaseException(previous: $e);
        }
    }
}
