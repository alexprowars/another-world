<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('logs_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users');
			$table->string('action');
			$table->string('item');
			$table->timestamp('date')->useCurrent();
			$table->string('place')->nullable();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('logs_items');
	}
};
