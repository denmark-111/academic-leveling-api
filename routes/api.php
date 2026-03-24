<?php

use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\StudySessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/quizzes/mine', [QuizController::class, 'myQuizzes']);
    Route::apiResource('quizzes', QuizController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('/quizzes/{quiz}/attempts', [AttemptController::class, 'start']); // Start the quiz
    Route::post('/attempts/{attempt}/answers', [AttemptController::class, 'saveAnswer']); // Answer a question
    Route::post('/attempts/{attempt}/submit', [AttemptController::class, 'submit']); // Submit the quiz

    Route::apiResource('study-sessions', StudySessionController::class)->only(['index', 'store']);
});