<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('menu_order', function (Blueprint $table) {
			// Relasi ke tabel orders dan menus
			$table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
			$table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');

			$table->integer('quantity');
			$table->integer('subtotal_price');
			$table->integer('unit_price');

			// Menetapkan composite primary key
			$table->primary(['order_id', 'menu_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('orders_menus');
	}
};
