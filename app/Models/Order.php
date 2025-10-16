<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  use HasFactory;

  /**
   * Atribut yang dapat diisi secara massal.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'status',
    'total_price',
  ];

  // --- RELASI ---

  /**
   * Relasi one-to-many (inverse): Setiap order dimiliki oleh satu user.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Relasi many-to-many: Setiap order bisa memiliki banyak menu.
   */
  public function menus()
  {
    return $this
      ->belongsToMany(Menu::class, 'menu_order')
      ->withPivot('quantity', 'subtotal_price');
  }
}
