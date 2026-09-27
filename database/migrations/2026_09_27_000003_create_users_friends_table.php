<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('users_friends', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
			$table->foreignId('friend_id')->constrained('users')->cascadeOnDelete();
			$table->boolean('is_ignored')->default(false);
			$table->unique(['user_id', 'friend_id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('users_friends');
	}
};
