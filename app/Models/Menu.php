<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Authenticatable
{
  use HasFactory;

  protected $fillable = [
        'name',
        'price',
        'stock',
        'type',
  ];

  public function orders()
  {
    return $this->belongsToMany(Order::class, 'menu_order')
                ->withPivot('subtotal_price')
                ->withTimestamps();
  }
}
