<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'price',
    'stock',
    'image',
    'type',
  ];

  /**
   * Menambahkan atribut 'image_url' secara otomatis ke model
   * saat diubah menjadi array atau JSON.
   */
  protected $appends = ['image_url'];

  /**
   * Accessor untuk mendapatkan URL lengkap dari gambar.
   * Akan membuat URL default jika tidak ada gambar.
   *
   * @return string
   */
  public function getImageUrlAttribute()
  {
    if ($this->image && Storage::disk('public')->exists($this->image)) {
      return Storage::disk('public')->url($this->image);
    }

    // Ganti dengan path gambar default Anda
    return Storage::disk('public')->url('menus/file-not-found.jpg');
  }
}
