<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('tribe_requests', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
			$table->unsignedTinyInteger('status')->default(0);
			$table->timestamps();
			$table->index('created_at');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('tribe_requests');
	}
};
