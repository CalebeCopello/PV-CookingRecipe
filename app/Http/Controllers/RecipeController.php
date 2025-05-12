<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index(Request $request)
    {

        $order = $request->validate([
            'order' => 'in:asc,desc'
        ]);

        $order = $order['order'] ?? 'desc';
        $recipes = Auth::user()->recipes()->orderBy('created_at', $order)->get();
        if ($recipes->isEmpty()) {
            return response()->json(['message' => 'No recipes found'], 404);
        }
        return response()->json($recipes);
    }

    public function show($id)
    {
        $recipe = Auth::user()->recipes()->find($id);
        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found.'], 404);
        }

        return response()->json($recipe);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'ingredients' => 'required|string|max:2000',
            'instructions' => 'required|string|max:5000'
        ]);

        $recipe = Auth::user()->recipes()->create($fields);

        return response()->json([
            'message' => 'Recipe created successfully.',
            'recipe' => $recipe
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $recipe = Auth::user()->recipes()->find($id);
        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found.'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'ingredients' => 'required|string|max:2000',
            'instructions' => 'required|string|max:5000'
        ]);
        $recipe->update($validated);

        return response()->json([
            'message' => 'Recipe updated successfully.',
            'recipe' => $recipe
        ]);
    }

    public function destroy($id)
    {
        $recipe = Auth::user()->recipes()->find($id);
        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found.'], 404);
        }

        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted successfully.']);
    }

    public function showInfo($id)
    {
        $recipe = Recipe::with(['comments', 'ratings'])->find($id);
        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found.'], 404);
        }
        $average = number_format($recipe->ratings()->avg('rating'), 1, '.', '');
        $return = [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'description' => $recipe->description,
            'avarage_rating' => $average,
            'ratings' => $recipe->ratings->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                ];
            }),
            'comments' => $recipe->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'author' => $comment->author,
                    'comment' => $comment->content,
                    'created_at' => $comment->created_at->toDateTimeString(),
                ];
            }),
        ];
        return response()->json([$return]);
    }
}
