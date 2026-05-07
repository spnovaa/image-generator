<?php

namespace App\Contracts;

use App\Exceptions\GeneralDatabaseException;
use App\Models\RequestHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Repository abstraction for {@see RequestHistory}.
 *
 * The domain code depends on this contract instead of Eloquent directly,
 * which keeps services testable (in-memory fakes) and storage-agnostic.
 */
interface RequestHistoryRepository
{
    /** @throws GeneralDatabaseException */
    public function save(RequestHistory $history): RequestHistory;

    /** @throws GeneralDatabaseException */
    public function find(int $id): ?RequestHistory;

    /**
     * Paginated index, newest first.
     *
     * @throws GeneralDatabaseException
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Records that have a caption ready and are awaiting image generation.
     *
     * @return Collection<int, RequestHistory>
     *
     * @throws GeneralDatabaseException
     */
    public function ready(): Collection;
}
