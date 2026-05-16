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
			$table->smallInteger('round')->nullable();
			$table->timestamp('date')->useCurrent();
			$table->foreignId('enemy_id')->nullable()->constrained('battles_members');
			$table->enum('hit', ['1','2','3','4','5'])->nullable();
			$table->enum('block', ['1','2','3','4','5'])->nullable();
			$table->smallInteger('damage')->nullable();
			$table->smallInteger('comment_id');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('battles_logs');
	}
};
