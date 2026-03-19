<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
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
});