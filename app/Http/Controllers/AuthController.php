<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController
{
	/**
	 * Menangani permintaan registrasi akun baru.
	 * User baru akan otomatis dibuatkan Cart.
	 */
	public function register(Request $request)
	{
		/** Validasi input dari request. */
		$validator = Validator::make($request->all(), [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:6',
		]);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message' => 'Validation error',
				'errors' => $validator->errors()
			], 422);
		}

		try {
			/**
			 * Gunakan Database Transaction untuk memastikan pembuatan User dan Cart
			 * berhasil sebagai satu kesatuan operasi yang tidak terpisahkan.
			 */
			$user = DB::transaction(function () use ($request) {
				/**
				 * Buat entitas User baru.
				 * Role di-set default ke 'customer'.
				 */
				$createdUser = User::create([
					'name' => $request->name,
					'email' => $request->email,
					'password' => Hash::make($request->password),
					'role' => 'customer',
				]);

				/** Buat entitas Cart yang terasosiasi dengan User yang baru dibuat. */
				$createdUser->cart()->create();

				return $createdUser;
			});

			/** Buat token otentikasi baru untuk user menggunakan Sanctum. */
			$token = $user->createToken('auth_token')->plainTextToken;

			/** Kirim response JSON yang berisi data user dan token. */
			return response()->json([
				'success' => true,
				'message' => 'User registered successfully',
				'data' => [
					'user' => $user,
					'token' => $token,
					'token_type' => 'Bearer',
				]
			], 201);
		} catch (\Exception $e) {
			/**
			 * Tangani jika terjadi exception selama proses transaksi.
			 * Mengembalikan response error 500.
			 */
			return response()->json([
				'success' => false,
				'message' => 'Registration failed. Please try again.',
				'error' => $e->getMessage()  // Opsional: hanya untuk debugging
			], 500);
		}
	}

	/**
	 * Menangani permintaan login user.
	 */
	public function login(Request $request)
	{
		/** Validasi email dan password dari request. */
		$request->validate([
			'email' => 'required|email',
			'password' => 'required',
			'remember_token' => 'nullable|string'
		]);

		/**
		 * Coba otentikasi user menggunakan kredensial yang diberikan.
		 * Auth::attempt() sudah menangani pencarian user dan verifikasi password.
		 */
		$credentials = $request->only('email', 'password');
		if (!Auth::attempt($credentials)) {
			return response()->json([
				'success' => false,
				'message' => 'Email or password is incorrect'
			], 401);  // 401 Unauthorized
		}

		/** Jika otentikasi berhasil, dapatkan instance User. */
		$user = Auth::user();

		/**
		 * Menghapus semua token lama dan membuat yang baru.
		 * Ini adalah strategi keamanan untuk memastikan user hanya memiliki
		 * satu sesi aktif, sehingga otomatis logout dari perangkat lain.
		 */
		$user->tokens()->delete();
		$token = $user->createToken('auth_token')->plainTextToken;

		return response()->json([
			'success' => true,
			'message' => 'Login successful',
			'data' => [
				'user' => $user,
				'token' => $token,
				'token_type' => 'Bearer',
			]
		], 200);
	}

	/**
	 * Menangani permintaan logout user.
	 */
	public function logout(Request $request)
	{
		/**
		 * Menggunakan $request->user() adalah cara modern untuk mendapatkan
		 * user yang terotentikasi dari request API yang dilindungi Sanctum.
		 */
		$user = $request->user();

		/**
		 * Hapus access token yang sedang digunakan untuk request ini.
		 * Ini hanya akan me-logout sesi dari perangkat saat ini.
		 */
		$user->currentAccessToken()->delete();

		return response()->json([
			'success' => true,
			'message' => 'Logout successful',
		], 200);
	}
}
