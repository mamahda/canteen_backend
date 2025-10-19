<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController
{
  /**
   * FITUR REAT
   * Menampilkan semua isi dari cart user yang sedang login
   */
  public function showCart()
  {
    $user = Auth::user();
    $cart = $user->cart;

    /**
     * Lakukan Loading untuk memuat relasi 'menus'.
     * Ini akan mengambil semua item menu yang ada di dalam keranjang user yang login
     */
    $cart->load('menus');

    return response()->json([
      'success' => true,
      'message' => 'User cart details retrieved successfully',
      'data' => $cart
    ], 200);
  }

  /**
   * FITUR UPDATE, UPDATE, DELETE
   * Menyelaraskan item di keranjang (menambah, memperbarui, atau menghapus).
   * Fungsi ini menggabungkan logika add, update, dan delete dalam satu endpoint.
   */
  public function updateCart(Request $request)
  {
    /**
     * Validasi input
     * Memastikan menu_id ada di database dan quantity minimal 0 (untuk hapus).
     */
    $validatedData = $request->validate([
      'menu_id' => ['required', 'integer', 'exists:menus,id'],
      'quantity' => ['required', 'integer', 'min:0']
    ]);

    $user = Auth::user();
    $cart = $user->cart;
    $menuId = $validatedData['menu_id'];
    $quantity = $validatedData['quantity'];

    try {
      $cartData = DB::transaction(function () use ($cart, $menuId, $quantity) {
        $menu = Menu::find($menuId);

        if ($quantity > 0) {
          /** Blok logika untuk menambah atau memperbarui item. */

          /**
           * Cek ketersediaan stok. Jika tidak cukup, lempar exception
           * untuk membatalkan transaksi secara otomatis.
           */
          if ($menu->stock < $quantity) {
            throw new \Exception('Stock is not sufficient');
          }

          $subtotalPrice = $quantity * $menu->price;

          /**
           * Gunakan syncWithoutDetaching untuk "update atau create".
           * Jika item belum ada, akan ditambahkan. Jika sudah ada, akan di-update.
           */
          $cart->menus()->syncWithoutDetaching([
            $menuId => [
              'quantity' => $quantity,
              'subtotal_price' => $subtotalPrice,
              'unit_price' => $menu->price
            ]
          ]);
        } else {
          /** Blok logika untuk menghapus item dari keranjang jika quantity adalah 0. */
          $cart->menus()->detach($menuId);
        }

        /**
         * Hitung ulang dan update total harga pada keranjang.
         * Ini dijalankan setiap kali ada perubahan (tambah, update, atau hapus).
         */
        $totalPrice = $cart->menus()->sum('subtotal_price');
        $cart->update(['total_price' => $totalPrice]);

        /** Kembalikan objek cart dari dalam closure transaksi. */
        return $cart;
      });

      /** Muat ulang relasi 'menus' untuk memastikan data yang dikembalikan adalah yang terbaru. */
      $cartData->load('menus');

      return response()->json([
        'success' => true,
        'message' => 'Cart updated successfully',
        'data' => $cartData
      ], 200);
    } catch (\Exception $e) {
      /**
       * Tangkap semua exception yang terjadi di dalam transaksi
       * (misalnya error stok) dan kembalikan sebagai response error.
       */
      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ], 400);
    }
  }
}