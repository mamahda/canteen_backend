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
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('role');  // Contoh: 'customer', 'admin'
			$table->rememberToken();
			$table->timestamps();  // Ini akan membuat created_at dan updated_at
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users');
	}
};
