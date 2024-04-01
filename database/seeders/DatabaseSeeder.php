<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
            ImageSeeder::class,
            CommentSeeder::class,
            TagSeeder::class,
            SpecificationSeeder::class,
            PlaceSeeder::class
        ]);
    }
}
