<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('academy', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->integer('duration');
			$table->smallInteger('price');
			$table->tinyInteger('level');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('academy');
	}
};
