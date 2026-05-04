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
			$table->smallInteger('br1')->nullable();
			$table->smallInteger('br2')->nullable();
			$table->smallInteger('br3')->nullable();
			$table->smallInteger('br4')->nullable();
			$table->smallInteger('br5')->nullable();
			$table->smallInteger('strength')->nullable();
			$table->smallInteger('dex')->nullable();
			$table->smallInteger('agility')->nullable();
			$table->smallInteger('vitality')->nullable();
			$table->smallInteger('power')->nullable();
			$table->smallInteger('razum')->nullable();
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
