<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    /*
     * Registration Tests.
     */
    #[Test]
    public function it_registrates_a_user()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => '123456'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token' => ['accessToken', 'plainTextToken']
        ]);

        $this->assertDatabaseHas('users', ['email' => 'testuser@example.com']);
    }
    #[Test]
    public function it_fails_registration_with_invalid_data()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }
    /*
     * Login Tests.
     */
    #[Test]
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token' => ['accessToken', 'plainTextToken'],
            ]);
    }
    #[Test]
    public function test_login_fails_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'wrongpass@example.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrongpass@example.com',
            'password' => '654321',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid Email or Password.']);
    }
    /*
     * Protected Route Test.
     */
    #[Test]
    public function test_unauthenticated_user_cannot_access_protected_route()
    {
        $response = $this->getJson('/api/recipes');

        $response->assertStatus(401);
    }
}
