<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
	 * Atribut yang dapat diisi secara massal.
	 *
	 * @var array<int, string>
	 */
    protected $fillable = [
        'user_id',
        'total_price',
    ];

    // --- RELASI ---

    /**
     * Relasi one-to-one (inverse): Setiap cart dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-many: Setiap cart bisa memiliki banyak menu.
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_cart')
                    ->withPivot('quantity', 'subtotal_price');
    }
}