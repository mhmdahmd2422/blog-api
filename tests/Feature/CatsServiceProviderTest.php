<?php

use App\Services\CatsAPI\CatsClientInterface;
use App\Services\CatsAPI\CatsServiceInterface;
use App\Services\CatsAPI\FakeCatsService;
use App\Services\CatsAPI\NullCatsClient;

it('returns null cats service for testing env', function () {
    expect(resolve(CatsServiceInterface::class))
        ->toBeInstanceOf(FakeCatsService::class);
});

it('returns null cats client for testing env', function () {
    expect(resolve(CatsClientInterface::class))
        ->toBeInstanceOf(NullCatsClient::class);
});
