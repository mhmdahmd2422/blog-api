<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->realText(),
            'image' => 'https://via.placeholder.com/200x100?text=blog_main_image',
        ];
    }

    public function unvisible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => false,
        ]);
    }
}
