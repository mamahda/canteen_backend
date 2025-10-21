<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderAdminController;
use App\Http\Controllers\OrderUserController;
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
Route::get('/menus', [MenuController::class, 'showAllMenu']); 

/**
 * Rute Terproteksi (Membutuhkan Otentikasi Sanctum)
 */
Route::middleware('auth:sanctum')->group(function () {
	/**
	 * Rute ini bisa diakses oleh semua user yang sudah login (admin & customer).
	 */
	Route::post('/logout', [AuthController::class, 'logout']);

	/**
	 * Rute ini hanya bisa diakses oleh user dengan role 'admin'.
	 */
	Route::middleware('role:admin')->group(function () {
		Route::post('/admin/menus', [MenuController::class, 'addMenu']);
		Route::post('/admin/menus/{menu}', [MenuController::class, 'uploadMenuImage']); 
		Route::patch('/admin/menus/{menu}', [MenuController::class, 'updateMenu']);
		Route::delete('/admin/menus/{menu}', [MenuController::class, 'deleteMenu']);

		Route::patch('admin/orders/{order}', [OrderAdminController::class, 'updateStatus']);
		Route::get('/admin/orders', [OrderAdminController::class, 'showAllOrder']);
	});

	/**
	 * Rute ini bisa diakses oleh user dengan role 'customer'.
	 */
	Route::middleware('role:customer')->group(function () {
		Route::get('/carts/', [CartController::class, 'showCart']);
		Route::patch('/carts/', [CartController::class, 'updateCart']);

		Route::get('/orders/', [OrderUserController::class, 'showOrder']);
		Route::post('/orders/', [OrderUserController::class, 'createOrder']);
		Route::patch('/orders/{order}', [OrderUserController::class, 'requestCancellation']);
	});
});