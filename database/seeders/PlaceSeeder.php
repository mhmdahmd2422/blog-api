<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\Specification;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        $places = Place::factory()->count(20)
            ->sequence(
            ['is_visible' => true],
            ['is_visible' => false],
        )->create();

        $places->each(function ($place) {
            $place->specifications()->attach(
                Specification::all()->random(rand(1, 3))->pluck('id')->toArray(),
                ['description' => fake()->sentence()]
            );
        });

        $places->each(function ($place) {
            $place->tags()->attach(
                Tag::all()->random(rand(1, 3))->pluck('id')->toArray(),
            );
        });
    }
}
