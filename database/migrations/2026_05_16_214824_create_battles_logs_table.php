<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('battles_logs', function (Blueprint $table) {
			$table->id();
			$table->foreignId('battle_id')->constrained('battles');
			$table->foreignId('member_id')->constrained('battles_members');
			$table->smallInteger('round')->default(0);
			$table->timestamp('date')->useCurrent();
			$table->foreignId('enemy_id')->nullable()->constrained('battles_members');
			$table->json('hit')->nullable();
			$table->json('block')->nullable();
			$table->json('enemy_block')->nullable();
			$table->smallInteger('damage')->nullable();
			$table->smallInteger('comment_id')->nullable();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('battles_logs');
	}
};
