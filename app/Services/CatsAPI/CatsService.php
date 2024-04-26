<?php

namespace App\Services\CatsAPI;

use App\Filters\CollectionFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CatsService implements CatsServiceInterface
{
    protected Collection|null $catCollection;

    public function __construct(protected CatsClient $catsClient)
    {
    }

    public function breeds(): CatsService
    {
        $this->catCollection = $this->cachedForWeek('catBreeds', 'breeds');

        return $this;
    }

    public function facts(): CatsService
    {
        $this->catCollection = $this->cachedForWeek('catFacts', 'facts');

        return $this;
    }

    public function randomFact(): CatsService
    {
        $this->catCollection = $this->cachedForWeek('catFacts', 'facts');

        return $this;
    }

    public function filter(CollectionFilter $filter): CatsService
    {
        if ($this->catCollection) {
            $this->catCollection = $filter->applyFilters($this->catCollection);
        }

        return $this;
    }

    public function get(): Collection|null
    {
        return $this->catCollection ?? null;
    }

    public function random(): array|null
    {
        return $this->get()?->random();
    }

    protected function cachedForWeek($key, $fetcher): Collection|null
    {
        $cachedData = Cache::remember($key, 60 * 24, function () use ($fetcher) {
            $response = call_user_func([$this->catsClient, $fetcher]);

            return $response->successful() ? $response->collect('data') : collect();
        });

        return $cachedData->isNotEmpty() ? $cachedData : null;
    }
}
