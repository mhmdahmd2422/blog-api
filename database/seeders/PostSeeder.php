<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory()->count(20)
            ->recycle(User::all())
            ->sequence(
                ['is_visible' => true],
                ['is_visible' => false],
            )
            ->create();
    }
}
