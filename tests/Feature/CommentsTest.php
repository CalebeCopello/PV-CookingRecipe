<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Recipe;
use PHPUnit\Framework\Attributes\Test;


class CommentsTest extends TestCase
{
    #[Test]
    public function guest_can_post_a_comment()
    {
        $recipe = Recipe::factory()->create();

        $payload = [
            'author' => 'John Doe',
            'content' => 'This recipe is amazing!'
        ];

        $response = $this->postJson("/api/recipes/{$recipe->id}/comments", $payload);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment created successfully.']);

        $this->assertDatabaseHas('comments', [
            'recipe_id' => $recipe->id,
            'author' => 'John Doe',
            'content' => 'This recipe is amazing!'
        ]);
    }

    #[Test]
    public function comment_requires_author_and_content()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->postJson("/api/recipes/{$recipe->id}/comments", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['author', 'content']);
    }
}
