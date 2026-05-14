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
			$table->unsignedTinyInteger('timeout');
			$table->enum('status', ['request', 'battle', 'finished'])->default('request');
			$table->string('comment')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('battles');
	}
};
