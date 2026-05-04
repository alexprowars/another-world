<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('shops_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
			$table->unsignedSmallInteger('section_id')->nullable()->index();
			$table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
			$table->unsignedSmallInteger('count');
			$table->unsignedSmallInteger('delivery');
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('shops_items');
	}
};
