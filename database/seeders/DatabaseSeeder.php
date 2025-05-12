<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        User::factory()->create([
            'name' => 'tester',
            'email' => 'test@example.com',
        ]);
        Recipe::factory(100)->create();
        Rating::factory(1500)->create();
        Comment::factory(600)->create();
    }
}
