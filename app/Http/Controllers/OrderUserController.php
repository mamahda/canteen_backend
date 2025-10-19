<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderUserController
{
	/**
	 * FITUR GET
	 */
	public function showOrder()
	{
		/** Dapatkan user yang sedang terotentikasi. */
		$user = Auth::user();

		/**
		 * Ambil semua order milik user tersebut.
		 * with('menus') agar detail menu di setiap pesanan
		 * juga ikut diambil dalam satu query yang efisien.
		 * latest() untuk mengurutkan dari yang paling baru.
		 */
		$orders = $user->orders()->with('menus')->latest()->get();

		return response()->json([
			'success' => true,
			'message' => 'User order history retrieved successfully',
			'data' => $orders
		], 200);
	}

	/**
	 * FITUR CREATE
	 * Membuat pesanan baru berdasarkan isi keranjang user yang sedang login.
	 */
	public function createOrder(Request $request)
	{
		$user = Auth::user();
		$cart = $user->cart->load('menus');

		if ($cart->menus->isEmpty()) {
			return response()->json([
				'success' => false,
				'message' => 'Cart is empty. Cannot create order.',
			], 400);
		}

		try {
			$order = DB::transaction(function () use ($user, $cart) {
				/** Validasi stok untuk SEMUA item terlebih dahulu */
				foreach ($cart->menus as $menu) {
					if ($menu->stock < $menu->pivot->quantity) {
						throw new \Exception("Stock for menu '{$menu->name}' is not sufficient.");
					}
				}

				/** Buat pesanan baru dengan total harga dari keranjang */
				$order = $user->orders()->create([
					'status' => 'Pending',
					'total_price' => $cart->total_price,
				]);

				/** Siapkan data item untuk disinkronkan ke order_menus */
				$itemsToSync = [];
				foreach ($cart->menus as $menu) {
					$itemsToSync[$menu->id] = [
						'quantity' => $menu->pivot->quantity,
						'unit_price' => $menu->pivot->unit_price,
						'subtotal_price' => $menu->pivot->subtotal_price,
					];

					/** Kurangi stok menu */
					$menu->decrement('stock', $menu->pivot->quantity);
				}

				/** Tambahkan semua item ke pesanan dalam satu query */
				$order->menus()->sync($itemsToSync);

				/** Kosongkan keranjang */
				$cart->menus()->detach();
				$cart->update(['total_price' => 0]);

				return $order;
			});

			/** muat relasi agar response berisi detail item */
			$order->load('menus');

			return response()->json([
				'success' => true,
				'message' => 'Order created successfully',
				'data' => $order
			], 201);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Failed to create order: ' . $e->getMessage(),
			], 400);
		}
	}

	/**
	 * FITUR UPDATE
	 * Memungkinkan user untuk meminta pembatalan order.
	 */
	public function requestCancellation(Order $order) 
	{
		/** Pengecekan Keamanan (Authorization) */
		if (Auth::id() !== $order->user_id) {
			return response()->json(['success' => false, 'message' => 'You do not own this order.'], 403);
		}

		/** Cek status */
		if ($order->status !== 'Pending') {
			return response()->json([
				'success' => false,
				'message' => 'Order cannot be cancelled because it is already being processed.'
			], 422);
		}

		/** Update status */
		$order->update(['status' => 'Request Cancellation']);

		/** Kembalikan response sukses */
		return response()->json([
			'success' => true,
			'message' => 'Cancellation request sent successfully. Please wait for seller approval.',
			'data' => $order
		], 200);
	}
};
