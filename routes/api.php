<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        // Khusus admin
        Route::middleware('role:admin')->group(function () {
            // route admin di sini
        });

        // Khusus customer
        Route::middleware('role:customer')->group(function () {
            // route customer di sini
        });

        // Route umum untuk user login
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});