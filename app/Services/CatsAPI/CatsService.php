<?php

namespace App\Services\CatsAPI;

use App\Filters\Website\CatsFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CatsService extends CatsFilter implements CatsServiceInterface
{
    public function __construct(protected CatsClient $catsClient)
    {
        parent::__construct();
    }

    public function breeds(): Collection|null
    {
        $breeds = $this->cachedForWeek('catBreeds', 'breeds');

        return $this->applyFilters($breeds) ?? null;
    }

    public function facts(): Collection|null
    {
        $facts = $this->cachedForWeek('catFacts', 'facts');

        return $this->applyFilters($facts) ?? null;
    }

    public function randomFact(): Collection|null
    {
        $facts = $this->cachedForWeek('catFacts', 'facts');

        if ($maxLength = request('max_length')) {
            $facts = $facts->where('length', '<=', $maxLength);
        }

        return $facts ? collect($this->applyFilters($facts)->random()) : null;
    }

    protected function cachedForWeek($key, $fetcher): Collection|null
    {
        return Cache::remember($key, 60 * 24, function () use ($key, $fetcher) {
            $response = call_user_func([$this->catsClient, $fetcher]);

            return $response->successful() ? $response->collect('data') : null;
        });
    }
}
