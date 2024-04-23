<?php

namespace App\Services\CatsAPI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CatsService
{
    public function allBreeds(int $paginationLength)
    {
        $facts = $this->cachedData('catBreeds', '/breeds');

        return $facts ? $facts->paginate($paginationLength) : null;
    }

    public function allFacts(int $paginationLength)
    {
        $paginationLength = request('limit', $paginationLength);

        $facts = $this->cachedData('catFacts', '/facts');

        if ($maxLength = request('max_length')) {
            $facts = $facts->where('length', '<=', $maxLength);
        }

        return $facts ? $facts->paginate(
            $paginationLength,
            $facts->count()
        )->withQueryString() : null;
    }

    public function randomFact()
    {
        $facts = $this->cachedData('catFacts', '/facts');

        if ($maxLength = request('max_length')) {
            $facts = $facts->where('length', '<=', $maxLength);
        }

        return $facts ? $facts->random() : null;
    }

    protected function cachedData($key, $path)
    {
        return Cache::remember($key, 60 * 24, function () use ($key, $path) {
                $total = Cache::remember($key.'Total' , 60 * 24, function () use ($path) {
                    $response = Http::catsApi()->get($path);

                    return $response->successful() ? $response['total'] : null;
                });

                $response = Http::catsApi()
                    ->withQueryParameters([
                        'limit' => $total
                    ])
                    ->get($path);

                return $response->successful() ? $response->collect('data') : null;
            });
    }
}
