<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('battles_members', function (Blueprint $table) {
			$table->id();
			$table->foreignId('battle_id')->constrained('battles');
			$table->foreignId('user_id')->constrained('users');
			$table->unsignedTinyInteger('side');
			$table->timestamp('finished_at')->nullable();
			$table->timestamp('died_at')->nullable();
			$table->unsignedInteger('damage')->default(0);
			$table->smallInteger('exp')->default(0);
			$table->smallInteger('wait')->default(0);
			$table->smallInteger('blocks')->default(0);
			$table->smallInteger('hits')->default(0);
			$table->smallInteger('hp')->default(0);
			$table->smallInteger('crits')->default(0);
			$table->smallInteger('parry')->default(0);
			$table->smallInteger('spirit')->default(0);
			$table->smallInteger('ability')->nullable();
			$table->smallInteger('time')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('battles_members');
	}
};
