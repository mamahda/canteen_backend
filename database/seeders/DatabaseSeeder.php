<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin',
            'password' => 'admin#123',
            'role' => 'admin',
        ]);

        Menu::factory()->create([
            'name' => 'Soto',
            'price' => '14000',
            'stock' => '20',
            'type' => 'Main Course',
        ]);

        Menu::factory()->create([
            'name' => 'Bakpau',
            'price' => '6000',
            'stock' => '10',
            'type' => 'Snack',
        ]);
        
        Menu::factory()->create([
            'name' => 'Le Minerale',
            'price' => '4000',
            'stock' => '50',
            'type' => 'Beverage',
        ]);
    }
}
