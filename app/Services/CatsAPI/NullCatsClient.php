<?php

namespace App\Services\CatsAPI;

use Illuminate\Http\Client\Response;

class NullCatsClient implements CatsClientInterface
{

    public function breeds(): Response
    {
        // TODO: Implement breeds() method.
    }

    public function facts(): Response
    {
        // TODO: Implement facts() method.
    }
}
