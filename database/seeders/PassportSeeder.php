<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportSeeder extends Seeder
{
    public function run(): void
    {
        $clientRepository = new ClientRepository();
        $this->client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', '/'
        );
    }
}
