<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('market_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_item_id')->unique()->constrained('users_items')->cascadeOnDelete();
			$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
			$table->decimal('price', 12, 2);
			$table->timestamps();
			$table->index('created_at');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('market_items');
	}
};