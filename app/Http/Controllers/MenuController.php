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
   * FITUR READ
   * Mendapatkan semua menu yang tersedia.
   * Bisa diakses oleh semua user.
   */
  public function showAllMenu(Request $request)
  {
    /**
     * Ambil nilai dari query parameter 'type'.
     * Jika tidak ada, nilainya akan null.
     */
    $tipeFilter = $request->query('type');

    /** Mulai query builder */
    $query = Menu::query();

    /** Jika parameter 'type' ada isinya, tambahkan filter ke query */
    if ($tipeFilter) {
      $query->where('type', $tipeFilter);
    }

    /** Eksekusi query dan ambil hasilnya */
    $menus = $query->get();

    return response()->json([
      'success' => true,
      'message' => 'List of menu items',
      'data' => $menus
    ], 200);
  }

  /**
   * FITUR CREATE
   * Menambahkan menu baru ke database.
   * Hanya bisa diakses oleh admin.
   */
  public function addMenu(Request $request)
  {
    /** Validasi input. Jika gagal, otomatis mengembalikan response 422. */
    $validatedData = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:menus'],
      'price' => ['required', 'integer', 'min:0'],
      'stock' => ['required', 'integer', 'min:0'],
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
   * FITUR UPDATE
   * Mengunggah gambar untuk menu yang sudah ada.
   * Hanya bisa diakses oleh admin.
   */
  public function uploadMenuImage(Request $request, Menu $menu)
  {
    /** Validasi file gambar. */
    $request->validate([
      'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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
      'data' => $menu
    ]);
  }

  /**
   * FITUR UPDATE
   * Mengupdate data menu yang sudah ada.
   * Hanya bisa diakses oleh admin.
   */
  public function updateMenu(Request $request, Menu $menu)
  {
    /**
     * Validasi: Gunakan 'sometimes' karena ini adalah PATCH.
     * Artinya, validasi hanya berjalan jika field tersebut dikirim di request.
     */
    $validatedData = $request->validate([
      'name' => [
        'sometimes',
        'string',
        'max:255',
        Rule::unique('menus')->ignore($menu->id)  // Abaikan nama menu ini sendiri
      ],
      'price' => ['sometimes', 'integer', 'min:0'],
      'stock' => ['sometimes', 'integer', 'min:0'],
      'type' => ['sometimes', 'string', Rule::in(['Main Course', 'Snack', 'Beverage'])],
    ]);

    /** Logika Update: Update model menu dengan data yang sudah divalidasi. */
    $menu->update($validatedData);

    /** Response: Kembalikan response sukses dengan data menu yang sudah terupdate. */
    return response()->json([
      'success' => true,
      'message' => 'Menu updated successfully',
      'data' => $menu
    ], 200);
  }

  /**
   * FITUR DELETE
   * Menghapus menu berdasarkan ID.
   * Hanya bisa diakses oleh admin.
   */
  public function deleteMenu(Menu $menu)
  {
    /** Hapus file gambar dari storage jika ada. */
    if ($menu->image && $menu->image != 'menus/file-not-found-jpg') {
      Storage::disk('public')->delete($menu->image);
    }

    $menu->delete();

    return response()->json([
      'success' => true,
      'message' => 'Menu deleted successfully'
    ], 200);
  }
}