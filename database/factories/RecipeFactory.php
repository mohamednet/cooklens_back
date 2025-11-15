<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'prep_time' => fake()->numberBetween(5, 30),
            'cook_time' => fake()->numberBetween(10, 120),
            'servings' => fake()->numberBetween(1, 8),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'status' => 'draft',
            'calories' => fake()->numberBetween(100, 800),
            'protein' => fake()->randomFloat(1, 5, 50),
            'carbs' => fake()->randomFloat(1, 10, 100),
            'fat' => fake()->randomFloat(1, 5, 50),
            'fiber' => fake()->randomFloat(1, 1, 20),
        ];
    }
}
