<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'API is running'
    ]);
});

// Bridge Route (Web → App)
Route::get('/reset-password', function (Request $request) {
    $token = $request->token;
    $email = $request->email;

    $scheme = env('APP_DEEP_LINK_SCHEME', 'academicleveling');

    return redirect("$scheme://reset-password?token=$token&email=$email");
});