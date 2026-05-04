<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('levels', function (Blueprint $table) {
			$table->id();
			$table->integer('level');
			$table->integer('up');
			$table->integer('base');
			$table->unsignedBigInteger('exp');
			$table->integer('credits')->default(0);
			$table->integer('updates')->default(0);
			$table->integer('raseup')->default(0);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('levels');
	}
};
