<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\Place;
use App\Models\Post;
use App\Models\Specification;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        Image::factory()->count(20)
            ->sequence(fn (Sequence $sequence) => [
                'imageable_type' => Post::class,
                'imageable_id' => ceil(($sequence->index + 1) / 2), // two images per post
                'is_main' => $sequence->index % 2 == 0, // only one main image per post
            ])
            ->create();

        Image::factory()->count(10)
            ->sequence(fn (Sequence $sequence) => [
                'imageable_type' => Category::class,
                'imageable_id' => $sequence->index + 1,
                'is_main' => true
            ])
            ->create();

        Image::factory()->count(5)
            ->sequence(fn (Sequence $sequence) => [
                'imageable_type' => Specification::class,
                'imageable_id' => $sequence->index + 1,
                'is_main' => true
            ])
            ->create();

        Image::factory()->count(20)
            ->sequence(fn (Sequence $sequence) => [
                'imageable_type' => Place::class,
                'imageable_id' => ceil(($sequence->index + 1) / 2), // two images per place
                'is_main' => $sequence->index % 2 == 0, // only one main image per place
            ])
            ->create();
    }
}
