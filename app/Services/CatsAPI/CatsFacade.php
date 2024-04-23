<?php

namespace App\Services\CatsAPI;

use Illuminate\Support\Facades\Facade;

class CatsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CatsServiceInterface::class;
    }
}
