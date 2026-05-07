<?php

namespace Tests\Fakes;

use App\Contracts\RequestHistoryRepository;
use App\Enums\RequestStatus;
use App\Models\RequestHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

/**
 * In-memory test double for {@see RequestHistoryRepository}.
 *
 * Demonstrates the value of depending on the contract: feature/unit
 * tests can swap out Eloquent entirely without touching domain code.
 */
final class InMemoryRequestHistoryRepository implements RequestHistoryRepository
{
    /** @var array<int, RequestHistory> */
    private array $records = [];

    private int $nextId = 1;

    public function save(RequestHistory $history): RequestHistory
    {
        if (! $history->id) {
            $history->id         = $this->nextId++;
            $history->created_at = now();
        }
        $history->updated_at      = now();
        $this->records[$history->id] = clone $history;

        return clone $this->records[$history->id];
    }

    public function find(int $id): ?RequestHistory
    {
        return isset($this->records[$id]) ? clone $this->records[$id] : null;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $items = array_values($this->records);
        usort($items, fn (RequestHistory $a, RequestHistory $b) => $b->id <=> $a->id);

        return new Paginator(array_slice($items, 0, $perPage), count($items), $perPage);
    }

    public function ready(): Collection
    {
        return collect($this->records)
            ->filter(fn (RequestHistory $h) => $h->status === RequestStatus::READY)
            ->values();
    }
}
