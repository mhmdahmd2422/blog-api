<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        Image::factory()->count(10)
            ->recycle(User::all())
            ->sequence(fn (Sequence $sequence) => [
                'imageable_type' => Post::class,
                'imageable_id' => $sequence->index + 1
            ])
            ->create();
    }
}
