<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		/** Membuat user admin */
		User::factory()->create([
			'name' => 'admin',
			'email' => 'admin@admin',
			'password' => Hash::make('admin#123'),
			'role' => 'admin',
		]);

		/** Data untuk Main Course */
		$mainCourses = [
			['name' => 'Nasi Goreng Spesial', 'price' => 18000],
			['name' => 'Mie Ayam Bakso', 'price' => 17000],
			['name' => 'Soto Ayam Lamongan', 'price' => 14000],
			['name' => 'Rawon Daging', 'price' => 20000],
			['name' => 'Sate Ayam Madura', 'price' => 15000],
			['name' => 'Gado-Gado Siram', 'price' => 12000],
			['name' => 'Ayam Bakar Taliwang', 'price' => 18000],
			['name' => 'Ikan Gurame Bakar', 'price' => 25000],
			['name' => 'Capcay Seafood', 'price' => 18000],
			['name' => 'Nasi Padang Rendang', 'price' => 17000]
		];

		foreach ($mainCourses as $menu) {
			Menu::factory()->create([
				'name' => $menu['name'],
				'price' => $menu['price'],
				'type' => 'Main Course',
			]);
		}

		/** Data untuk Snack */
		$snacks = [
			['name' => 'Bakwan Jagung', 'price' => 1000],
			['name' => 'Tahu Isi Pedas', 'price' => 1000],
			['name' => 'Risoles Mayo', 'price' => 3000],
			['name' => 'Lumpia Semarang', 'price' => 2000],
			['name' => 'Pastel Sayur', 'price' => 2000],
			['name' => 'Cireng Bumbu Rujak', 'price' => 10000],
			['name' => 'Tempe Mendoan', 'price' => 1000],
			['name' => 'Pisang Goreng Keju', 'price' => 12000],
			['name' => 'Siomay Bandung', 'price' => 10000],
			['name' => 'Martabak Telur Mini', 'price' => 5000]
		];

		foreach ($snacks as $menu) {
			Menu::factory()->create([
				'name' => $menu['name'],
				'price' => $menu['price'],
				'type' => 'Snack',
			]);
		}

		/** Data untuk Beverage */
		$beverages = [
			['name' => 'Es Teh Manis', 'price' => 4000],
			['name' => 'Es Jeruk Peras', 'price' => 5000],
			['name' => 'Jus Alpukat', 'price' => 10000],
			['name' => 'Kopi Hitam', 'price' => 6000],
			['name' => 'Teh Tarik', 'price' => 8000],
			['name' => 'Wedang Jahe', 'price' => 6000],
			['name' => 'Es Kelapa Muda', 'price' => 10000],
			['name' => 'Jus Mangga', 'price' => 12000],
			['name' => 'Soda Gembira', 'price' => 12000],
			['name' => 'Le Minerale', 'price' => 4000]
		];

		foreach ($beverages as $menu) {
			Menu::factory()->create([
				'name' => $menu['name'],
				'price' => $menu['price'],
				'type' => 'Beverage',
			]);
		}
	}
}