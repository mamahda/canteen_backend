<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * API Routes
 * --------------------------------------------------------------------------
 *
 * Rute-rute ini dimuat oleh RouteServiceProvider dalam grup yang
 * diberi middleware 'api'.
 *
 */

/**
 * Rute Publik (Tidak Perlu Otentikasi)
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/menus', [MenuController::class, 'getAllMenu']); // Diubah ke plural

/**
 * Rute Terproteksi (Membutuhkan Otentikasi Sanctum)
 */
Route::middleware('auth:sanctum')->group(function () {
	/**
	 * Rute ini hanya bisa diakses oleh user dengan role 'admin'.
	 * Menggunakan Gate 'admin-only' untuk otorisasi.
	 */
	Route::middleware('role:admin')->group(function () {
		Route::post('/menus', [MenuController::class, 'addMenu']); // Diubah ke plural
		Route::post('/menus/image/{menu}', [MenuController::class, 'uploadMenuImage']); // Route baru
		Route::delete('/menus/{menu}', [MenuController::class, 'deleteMenu']); // Route baru
	});

	/**
	 * Rute ini bisa diakses oleh user dengan role 'customer'.
	 * (Kosong untuk saat ini, bisa diisi nanti)
	 */
	Route::middleware('role:customer')->group(function () {
		// Contoh: Route::post('/orders', [OrderController::class, 'store']);
	});

	/**
	 * Rute ini bisa diakses oleh semua user yang sudah login (admin & customer).
	 */
	Route::post('/logout', [AuthController::class, 'logout']);
});