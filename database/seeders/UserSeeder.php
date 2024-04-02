<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create();

        if (App::environment() === 'local') {
            User::factory()
                ->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
        }
    }
}
