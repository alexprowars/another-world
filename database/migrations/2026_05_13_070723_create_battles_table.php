<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('battles', function (Blueprint $table) {
			$table->id();
			$table->timestamp('started_at')->nullable();
			$table->unsignedTinyInteger('type');
			$table->smallInteger('round')->default(1);
			$table->timestamp('round_at')->nullable();
			$table->smallInteger('timeout')->default(60);
			$table->boolean('use_weapons')->default(true);
			$table->boolean('is_blood')->default(false);
			$table->enum('status', ['waiting', 'active', 'finished', 'cancelled'])->default('waiting');
			$table->string('comment')->nullable();
			$table->smallInteger('capacity')->default(1);
			$table->smallInteger('min_level')->nullable();
			$table->smallInteger('max_level')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('battles');
	}
};
