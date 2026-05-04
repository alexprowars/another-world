<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('tribes', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('url')->nullable();
			$table->text('about')->nullable();
			$table->unsignedTinyInteger('sclon');
			$table->string('short');
			$table->unsignedInteger('points');
			$table->float('moneys', 2)->unsigned();
			$table->text('laws')->nullable();
			$table->string('logo')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('tribes');
	}
};
