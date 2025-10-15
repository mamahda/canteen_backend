<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasFactory, Notifiable, HasApiTokens;

	/**
	 * Atribut yang dapat diisi secara massal.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'role',
		'remember_token'
	];

	/**
	 * Atribut yang harus disembunyikan saat serialisasi.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Hash password secara otomatis
	 */
	protected function casts(): array
	{
		return [
			'password' => 'hashed',
		];
	}

	// --- RELASI ---

	/**
	 * Relasi one-to-one: Setiap user memiliki satu cart.
	 */
	public function cart()
	{
		return $this->hasOne(Cart::class);
	}

	/**
	 * Relasi one-to-many: Setiap user bisa memiliki banyak order.
	 */
	public function orders()
	{
		return $this->hasMany(Order::class);
	}
}
