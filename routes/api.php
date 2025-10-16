<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * API Routes
 * --------------------------------------------------------------------------
 *
 * Rute-rute ini dimuat oleh RouteServiceProvider dalam grup yang
 * diberi middleware 'api'.
 */

/**
 * Rute Publik (Tidak Perlu Otentikasi)
 * Untuk Authentikasi login dan register user
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/** 
 * Rute untuk menampilkan semua menu 
 */
Route::get('/menus', [MenuController::class, 'getAllMenu']); 

/**
 * Rute Terproteksi (Membutuhkan Otentikasi Sanctum)
 */
Route::middleware('auth:sanctum')->group(function () {
	/**
	 * Rute ini hanya bisa diakses oleh user dengan role 'admin'.
	 */
	Route::middleware('role:admin')->group(function () {
		Route::post('/menus', [MenuController::class, 'addMenu']);
		Route::post('/menus/image/{menu}', [MenuController::class, 'uploadMenuImage']); 
		Route::patch('/menus/{menu}', [MenuController::class, 'updateMenu']);
		Route::delete('/menus/{menu}', [MenuController::class, 'deleteMenu']);
	});

	/**
	 * Rute ini bisa diakses oleh user dengan role 'customer'.
	 */
	Route::middleware('role:customer')->group(function () {
		Route::get('cart/', [CartController::class, 'showCart']);
		Route::patch('cart/', [CartController::class, 'addMenutoCart']);
	});

	/**
	 * Rute ini bisa diakses oleh semua user yang sudah login (admin & customer).
	 */
	Route::post('/logout', [AuthController::class, 'logout']);
});