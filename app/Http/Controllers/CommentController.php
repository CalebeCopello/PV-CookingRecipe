<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class CommentController extends Controller
{
    public function store(Request $request, Recipe $recipe)
{
    $validated = $request->validate([
        'author' => 'required|string|max:150',
        'content' => 'required|string|max:700',
    ]);

    $recipe->comments()->create($validated);

    return response()->json(['message' => 'Comment created successfully.']);
}
}
