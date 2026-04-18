<?php

use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\StudySessionController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return UserResource::make($request->user());
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/quizzes/mine', [QuizController::class, 'myQuizzes']);
    Route::apiResource('quizzes', QuizController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('/quizzes/{quiz}/attempts', [AttemptController::class, 'start']); // Start the quiz
    Route::post('/attempts/{attempt}/answers', [AttemptController::class, 'saveAnswer']); // Answer a question
    Route::post('/attempts/{attempt}/submit', [AttemptController::class, 'submit']); // Submit(finish) the quiz
    Route::post('/attempts/{attempt}/submit-all', [AttemptController::class, 'submitAll']); // Submit all answers at once

    Route::apiResource('attempts', AttemptController::class)->only(['index', 'show']);

    Route::apiResource('study-sessions', StudySessionController::class)->only(['index', 'store']);

    Route::get('/quests', [QuestController::class, 'index']);
    Route::post('/quests/{quest}/claim', [QuestController::class, 'claim']);

    // Achievements
    Route::get('/achievements', [App\Http\Controllers\Api\AchievementController::class, 'index']);
    Route::post('/achievements/{achievement}/claim', [App\Http\Controllers\Api\AchievementController::class, 'claim']);

    // Shop
    Route::get('/shop/items', [App\Http\Controllers\Api\ShopController::class, 'index']);
    Route::post('/shop/buy/{item}', [App\Http\Controllers\Api\ShopController::class, 'buy']);
    Route::get('/user/inventory', [App\Http\Controllers\Api\ShopController::class, 'inventory']);
    Route::post('/user/inventory/use/{userItem}', [App\Http\Controllers\Api\ShopController::class, 'useItem']);
});