<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('tribe_logs', function (Blueprint $table) {
			$table->id();
			$table->foreignId('tribe_id')->constrained('tribes');
			$table->foreignId('user_id')->constrained('users');
			$table->foreignId('target_user_id')->nullable()->constrained('users');
			$table->text('action');
			$table->timestamps();
			$table->index(['tribe_id', 'id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('tribe_logs');
	}
};
