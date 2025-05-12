<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Rating;
use App\Models\Comment;

class RecipeTest extends TestCase
{
    /*
     * Registration Recipe Tests.
     */
    #[Test]
    public function test_user_can_create_recipe()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $payload = [
            "title" => "Bolo de Cenoura",
            "description" => "Versão Tradicional",
            "ingredients" => "cenoura, ovos, farinha, açúcar",
            "instructions" => "Misture e asse por 35 minutos."
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/recipes', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                "message",
                "recipe" =>  [
                    'title',
                    'description',
                    'ingredients',
                    'instructions',
                    "user_id",
                    "updated_at",
                    "created_at",
                    "id"
                ]
            ]);

        $this->assertDatabaseHas('recipes', ["title" => "Bolo de Cenoura"]);
    }
    /*
     * View Recipe Tests.
     */
    #[Test]
    public function test_user_can_view_recipe()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/recipes/' . $recipe->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'ingredients',
                'instructions',
                'user_id',
                'created_at',
                'updated_at'
            ]);
    }
    /*
     * Update Recipe Tests.
     */
    #[Test]
    public function test_user_can_update_recipe()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $payload = [
            "title" => "Bolo de Laranja",
            "description" => "Bolo de laranja",
            "ingredients" => "laranja, ovos, farinha, açúcar",
            "instructions" => "Misture e asse por 35 minutos."
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/recipes/' . $recipe->id, $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Recipe updated successfully.']);

        $this->assertDatabaseHas('recipes', ["title" => "Bolo de Laranja"]);
    }
    /*
     * Delete Recipe Tests.
     */
    #[Test]
    public function test_user_can_delete_recipe()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $recipe = $user->recipes()->create([
            'title' => 'Test Recipe',
            'description' => 'Test Description',
            'ingredients' => 'Test Ingredients',
            'instructions' => 'Test Instructions',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/recipes/' . $recipe->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Recipe deleted successfully.']);

        $this->assertDatabaseHas('recipes', ['id' => $recipe->id, 'deleted_at' => now()]);
    }

    /*
     * Unauthorized User Recipe Tests.
     */
    #[Test]
    public function test_unauthenticated_user_cannot_update_recipe()
    {
        $recipe = Recipe::factory()->create();

        $payload = [
            'title' => 'Updated Recipe Title',
            'description' => 'Updated description for the recipe',
            'ingredients' => 'Updated Ingredient 1, Ingredient 2',
            'instructions' => 'Updated Step 1, Step 2',
        ];

        $response = $this->putJson('/api/recipes/' . $recipe->id, $payload);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_unauthenticated_user_cannot_delete_recipe()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->deleteJson('/api/recipes/' . $recipe->id);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }


    #[Test]
    public function it_returns_recipe_with_comments_and_ratings()
    {
        $recipe = Recipe::factory()->create([
            'title' => 'Spaghetti',
            'description' => 'Classic Italian pasta',
        ]);

        Rating::factory()->create(['recipe_id' => $recipe->id, 'rating' => 4]);
        Rating::factory()->create(['recipe_id' => $recipe->id, 'rating' => 5]);

        Comment::factory()->create([
            'recipe_id' => $recipe->id,
            'author' => 'Alice',
            'content' => 'Delicious!',
        ]);

        $response = $this->getJson("/api/recipe/{$recipe->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $recipe->id,
                'title' => 'Spaghetti',
                'description' => 'Classic Italian pasta',
                'average_rating' => '4.5',
            ])
            ->assertJsonStructure([
                [
                    'id',
                    'title',
                    'description',
                    'average_rating',
                    'ratings' => [['id', 'rating']],
                    'comments' => [['id', 'author', 'comment', 'created_at']],
                ]
            ]);
    }

    #[Test]
    public function it_returns_404_if_recipe_does_not_exist()
    {
        $response = $this->getJson('/api/recipe/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Recipe not found.']);
    }
}
