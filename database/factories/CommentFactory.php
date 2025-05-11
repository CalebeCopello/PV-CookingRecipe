<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $recipeId = Recipe::inRandomOrder()->value('id');
        return [
            'recipe_id' => $recipeId,
            'author' => $this->faker->name(),
            'content' => $this->faker->sentence(),
        ];
    }
}
