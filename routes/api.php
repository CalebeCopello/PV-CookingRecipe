<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->delete('/logout', [AuthController::class, 'logout']);

//Recipes Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::get('/recipes/{id}', [RecipeController::class, 'show']);
    Route::put('/recipes/{id}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);
});


Route::post('/recipes/{recipe}/ratings', [RatingController::class, 'store']);
Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store']);