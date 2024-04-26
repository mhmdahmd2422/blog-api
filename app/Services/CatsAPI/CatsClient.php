<?php

namespace App\Services\CatsAPI;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CatsClient implements CatsClientInterface
{
    protected string $baseUri = 'https://catfact.ninja';

    public function breeds(): Response
    {
        return Http::withQueryParameters([
            'limit' => Http::get("{$this->baseUri}/breeds")['total']
        ])->get("{$this->baseUri}/breeds");
    }

    public function facts(): Response
    {
        return Http::withQueryParameters([
                'limit' => Http::get("{$this->baseUri}/facts")['total']
            ])->get("{$this->baseUri}/facts");
    }
}
