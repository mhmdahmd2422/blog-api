<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::factory()->count(20)->recycle(User::all())
            ->sequence(
                ['is_visible' => true],
                ['is_visible' => false],
            )->create();

        $posts->each(function ($post) {
            $post->categories()->attach(
                Category::all()->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
