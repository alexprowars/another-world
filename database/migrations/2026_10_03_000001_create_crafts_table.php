<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('crafts', function (Blueprint $table) {
			$table->id();
			$table->foreignId('item_id')->unique()->constrained('items')->cascadeOnDelete();
			$table->json('ingredients');
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('crafts');
	}
};
