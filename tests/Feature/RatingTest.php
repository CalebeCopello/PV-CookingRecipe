<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Recipe;
use PHPUnit\Framework\Attributes\Test;


class RatingTest extends TestCase
{
    #[Test]
    public function guest_can_rate_a_recipe()
    {
        $recipe = Recipe::factory()->create();

        $payload = [
            'rating' => 5
        ];

        $response = $this->postJson("/api/recipes/{$recipe->id}/ratings", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Rating stored successfully.'
            ]);

        $this->assertDatabaseHas('ratings', [
            'recipe_id' => $recipe->id,
            'rating' => 5,
        ]);
    }

    #[Test]
    public function rating_requires_valid_input()
    {
        $recipe = Recipe::factory()->create();

        $payload = [
            'rating' => 10
        ];

        $response = $this->postJson("/api/recipes/{$recipe->id}/ratings", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }
}
