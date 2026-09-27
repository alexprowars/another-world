<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('vaults', function (Blueprint $table) {
			$table->unsignedSmallInteger('id')->primary();
			$table->string('title');
			$table->unsignedInteger('time');
			$table->unsignedSmallInteger('top_id')->nullable();
			$table->unsignedSmallInteger('bottom_id')->nullable();
			$table->unsignedSmallInteger('left_id')->nullable();
			$table->unsignedSmallInteger('right_id')->nullable();
			$table->text('text');
			$table->timestamp('heal_at')->nullable();
		});

		Schema::table('users', function (Blueprint $table) {
			$table->unsignedSmallInteger('vault_destination_id')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn('vault_destination_id');
		});

		Schema::dropIfExists('vaults');
	}
};
