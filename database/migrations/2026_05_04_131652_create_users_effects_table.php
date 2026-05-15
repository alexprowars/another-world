<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('effects', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
			$table->unsignedTinyInteger('type');
			$table->timestamp('date')->nullable();
			$table->smallInteger('armor1')->nullable();
			$table->smallInteger('armor2')->nullable();
			$table->smallInteger('armor3')->nullable();
			$table->smallInteger('armor4')->nullable();
			$table->smallInteger('armor5')->nullable();
			$table->smallInteger('strength')->nullable();
			$table->smallInteger('dexterity')->nullable();
			$table->smallInteger('agility')->nullable();
			$table->smallInteger('vitality')->nullable();
			$table->smallInteger('magic')->nullable();
			$table->smallInteger('intelligence')->nullable();
			$table->smallInteger('battery')->nullable();
			$table->smallInteger('min')->nullable();
			$table->smallInteger('max')->nullable();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('effects');
	}
};
