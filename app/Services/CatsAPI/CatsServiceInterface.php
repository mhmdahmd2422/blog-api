<?php

namespace App\Services\CatsAPI;

use Illuminate\Support\Collection;

interface CatsServiceInterface
{
    public function breeds(): Collection|null;

    public function facts(): Collection|null;

    public function randomFact(): Collection|null;
}
