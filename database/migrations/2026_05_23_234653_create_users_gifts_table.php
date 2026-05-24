<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('users_gifts', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
			$table->timestamp('date')->useCurrent();
			$table->tinyInteger('from')->default(1);
			$table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
			$table->nullableMorphs('sender');
			$table->string('text')->nullable();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('users_gifts');
	}
};
