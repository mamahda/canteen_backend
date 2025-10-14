<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Authenticatable
{
  use HasFactory;

  protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'payment_method',
  ];

  public function Menu()
  {
    return $this->belongsToMany(Order::class, 'menu_order')
                ->withPivot('subtotal_price')
                ->withTimestamps();
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

}
