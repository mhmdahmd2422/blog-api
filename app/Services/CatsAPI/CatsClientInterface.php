<?php

namespace App\Services\CatsAPI;

use Illuminate\Http\Client\Response;

interface CatsClientInterface
{
    public function breeds(): Response;

    public function facts(): Response;
}
