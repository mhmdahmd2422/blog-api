<?php

namespace App\Services\CatsAPI;

use Illuminate\Support\Collection;

class FakeCatsService implements CatsServiceInterface
{

    public function breeds(): ?Collection
    {
        return [];
    }

    public function facts(): ?Collection
    {
        return [];
    }

    public function randomFact(): ?Collection
    {
        return [];
    }
}
