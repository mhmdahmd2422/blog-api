<?php

namespace App\Services\CatsAPI;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CatsClient implements CatsClientInterface
{
    public function breeds(): Response
    {
        return Http::catsApi()->withQueryParameters([
            'limit' => Http::catsApi()->get('/breeds')['total']
        ])->get('/breeds');
    }

    public function facts(): Response
    {
        return Http::catsApi()->withQueryParameters([
                'limit' => Http::catsApi()->get('/facts')['total']
            ])->get('/facts');
    }
}
