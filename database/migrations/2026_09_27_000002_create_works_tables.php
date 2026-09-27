<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('work_types', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->unsignedTinyInteger('level')->default(0);
			$table->unsignedSmallInteger('activity');
		});

		Schema::create('works', function (Blueprint $table) {
			$table->id();
			$table->foreignId('work_type_id')->constrained('work_types')->cascadeOnDelete();
			$table->string('title');
			$table->unsignedInteger('duration');
			$table->unsignedSmallInteger('price');
		});

		Schema::table('users', function (Blueprint $table) {
			$table->foreignId('work_id')->nullable()->constrained('works')->nullOnDelete();
		});
	}

	public function down(): void
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropConstrainedForeignId('work_id');
		});

		Schema::dropIfExists('works');
		Schema::dropIfExists('work_types');
	}
};
