<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Cart;
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
         * Lakukan "Eager Loading" untuk memuat relasi 'menus'.
         * Ini akan mengambil semua item menu yang ada di dalam keranjang ini,
         * lengkap dengan data dari tabel pivot (quantity, subtotal_price).
         */
        $cart->load('menus');

        return response()->json([
            'success' => true,
            'message' => 'User cart details retrieved successfully', 
            'data' => $cart
        ], 200);
  }

  /**
   * FITUR UPDATE
   * Menambahkan menu pada keranjang user yang sedang login
   */
  public function addMenutoCart(Request $request)
  {
    /** Validasi input dari user */
    $validateData = $request->validate([
      'menu_id' => ['required', 'integer'],
      'quantity' => ['required', 'integer']
    ]);

    $user = Auth::user();
    $menu = Menu::find($request->menu_id);
    $cart = $user->cart;

    /** Cek ketersediaan stock */
    if ($menu->stock < $request->quantity) {
      return response()->json([
        'success' => false,
        'message' => 'Stock is not sufficient'
      ]);
    }

    try {
      DB::transaction(function () use ($user, $menu, $cart, $request) {
        /** Cek apakah menu sudah terdaftar pada cart */
        $menuExist = $cart->menus()->where('menu_id', $menu->id)->first();

        if ($menuExist) {
          /** Jika ada update jumlah quantity dan total harga menu pada tabel pivot (menu_cart) */
          $newQuantity = $menuExist->pivot->quantity + $request->quantity;
          $newSubPrice = $menu->price * $newQuantity;

          /** Cek apakah stock tersedia dengan quantity yang baru */
          if ($newQuantity > $menu->stock) {
            throw new \exception('Stock is not sufficient');
          }

          /** Update quantity dan subtotal_price dari tabel pivot */
          $cart->menus()->updateExistingPivot($menu->id, [
            'quantity' => $newQuantity,
            'subtotal_price' => $newSubPrice,
            'unit_price' => $menu->price
          ]);
        } else {
          /** Jika tidak ada attach tabel pivot baru */
          $subPrice = $request->quantity * $menu->price;
          $cart->menus()->attach($menu->id, [
            'quantity' => $request->quantity,
            'subtotal_price' => $subPrice,
            'unit_price' => $menu->price
          ]);
        }
        /** Hitung dan update total harga pada cart */
        $totalPrice = $cart->menus()->sum('subtotal_price');
        $cart->update(['total_price' => $totalPrice]);
      });

      /** Muat ulang relasi untuk mendapatkan data terbaru */
      $cart->load('menus');

      return response()->json([
        'success' => true,
        'message' => 'Item added to cart successfully',
        'data' => $cart
      ], 200);
    } catch (\exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ], 400);
    }
  }
}

?>