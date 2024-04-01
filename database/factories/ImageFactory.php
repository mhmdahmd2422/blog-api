<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'path' => 'public/images/placeholder.png'
        ];
    }

    public function is_main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => true,
        ]);
    }

    public function not_main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => false,
        ]);
    }
}
