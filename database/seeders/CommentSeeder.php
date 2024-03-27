<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
         Comment::factory()->count(200)
             ->recycle(Post::all())
             ->recycle(User::all())
             ->create();

        Comment::factory()->count(50)
            ->banned()
            ->recycle(Post::all())
            ->recycle(User::all())
            ->create();
    }
}
