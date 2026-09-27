<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('donations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
			$table->decimal('amount', 12, 2);
			$table->string('comment', 100)->nullable();
			$table->timestamps();
			$table->index('created_at');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('donations');
	}
};