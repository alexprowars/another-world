<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('battles_members', function (Blueprint $table) {
			$table->id();
			$table->foreignId('battle_id')->constrained('battles');
			$table->foreignId('user_id')->constrained('users');
			$table->unsignedTinyInteger('side');
			$table->timestamp('died_at')->nullable();
			$table->unsignedInteger('damage')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('battles_members');
	}
};
