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
		Schema::create('menus', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->integer('price');
			$table->integer('stock');
			$table->string('image')->nullable();  // Gambar bisa jadi opsional
			$table->string('type');  // Contoh: 'main Course', 'snack', 'beverage'
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('menus');
	}
};
