<?php

namespace App\Services\CatsAPI;

use Illuminate\Support\Collection;

interface CatsServiceInterface
{
    public function breeds(): CatsService;

    public function facts(): CatsService;

    public function randomFact(): CatsService;

    public function get(): Collection|null;
}
