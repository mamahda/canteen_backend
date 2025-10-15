<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuController
{
  /**
   * Menambahkan menu baru ke database.
   * Hanya bisa diakses oleh admin.
   */
  public function addMenu(Request $request)
  {
    /** Validasi input. Jika gagal, otomatis mengembalikan response 422. */
    $validatedData = $request->validate([
      'name' => 'required|string|max:255|unique:menus',
      'price' => 'required|integer|min:0',
      'stock' => 'required|integer|min:0',
      'type' => ['required', 'string', Rule::in(['Main Course', 'Snack', 'Beverage'])],
    ]);

    $menu = Menu::create($validatedData);

    return response()->json([
      'success' => true,
      'message' => 'Menu added successfully',
      'data' => $menu
    ], 201);
  }

  /**
   * Mengunggah gambar untuk menu yang sudah ada.
   * Hanya bisa diakses oleh admin.
   */
  public function uploadMenuImage(Request $request, Menu $menu)
  {
    /** Otorisasi: Cek apakah user yang login adalah admin. */
    if (Auth::user()->role !== 'admin') {
      return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    /** Validasi file gambar. */
    $request->validate([
      'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    /** Hapus gambar lama jika ada. */
    if ($menu->image) {
      Storage::disk('public')->delete($menu->image);
    }

    /** Simpan gambar baru dan update path di database. */
    $imagePath = $request->file('image')->store('menus', 'public');
    $menu->update(['image' => $imagePath]);

    return response()->json([
      'success' => true,
      'message' => 'Image uploaded successfully',
      'data' => $menu  // Mengembalikan data menu terbaru dengan image_url
    ]);
  }

  /**
   * Mendapatkan semua menu yang tersedia.
   * Bisa diakses oleh semua user.
   */
  public function getAllMenu()
  {
    /**
     * Ambil semua menu. Atribut 'image_url' akan otomatis ditambahkan
     * oleh Accessor yang kita buat di model.
     */
    $menus = Menu::all();

    return response()->json([
      'success' => true,
      'message' => 'List of all menu items',
      'data' => $menus
    ], 200);
  }

  /**
   * Menghapus menu berdasarkan ID.
   * Hanya bisa diakses oleh admin.
   */
  public function deleteMenu(Menu $menu)
  {
    /** Otorisasi: Cek apakah user yang login adalah admin. */
    if (Auth::user()->role !== 'admin') {
      return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    /** Hapus file gambar dari storage jika ada. */
    if ($menu->image) {
      Storage::disk('public')->delete($menu->image);
    }

    $menu->delete();

    return response()->json([
      'success' => true,
      'message' => 'Menu deleted successfully'
    ], 200);
  }
}
