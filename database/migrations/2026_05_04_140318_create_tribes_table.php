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
			$table->decimal('moneys', 12, 2)->unsigned()->default(0);
			$table->text('laws')->nullable();
			$table->string('logo')->nullable();
			$table->timestamps();
		});

		Schema::table('users', function (Blueprint $table) {
			$table->foreign('tribe_id')->references('id')->on('tribes')->nullOnDelete();
		});

		Schema::table('users_items', function (Blueprint $table) {
			$table->foreign('tribe_id')->references('id')->on('tribes')->restrictOnDelete();
		});
	}

	public function down(): void
	{
		Schema::table('users_items', function (Blueprint $table) {
			$table->dropForeign(['tribe_id']);
		});

		Schema::table('users', function (Blueprint $table) {
			$table->dropForeign(['tribe_id']);
		});

		Schema::dropIfExists('tribes');
	}
};
