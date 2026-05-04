<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('users_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
			$table->string('inf')->nullable();
			$table->string('min')->nullable();
			$table->unsignedTinyInteger('tip');
			$table->smallInteger('br1')->nullable();
			$table->smallInteger('br2')->nullable();
			$table->smallInteger('br3')->nullable();
			$table->smallInteger('br4')->nullable();
			$table->smallInteger('br5')->nullable();
			$table->smallInteger('br_m')->nullable();
			$table->smallInteger('min_d')->nullable();
			$table->smallInteger('max_d')->nullable();
			$table->smallInteger('hp')->nullable();
			$table->smallInteger('energy')->nullable();
			$table->smallInteger('strength')->nullable();
			$table->smallInteger('dex')->nullable();
			$table->smallInteger('agility')->nullable();
			$table->smallInteger('vitality')->nullable();
			$table->smallInteger('razum')->nullable();
			$table->smallInteger('krit')->nullable();
			$table->smallInteger('mkrit')->nullable();
			$table->smallInteger('unkrit')->nullable();
			$table->smallInteger('uv')->nullable();
			$table->smallInteger('unuv')->nullable();
			$table->smallInteger('pblock')->nullable();
			$table->tinyInteger('mblock')->nullable();
			$table->smallInteger('pbr')->nullable();
			$table->smallInteger('kbr')->nullable();
			$table->timestamp('time')->nullable();
			$table->unsignedInteger('life')->nullable();
			$table->unsignedTinyInteger('present')->nullable();
			$table->unsignedTinyInteger('bank')->nullable();
			$table->unsignedTinyInteger('onset')->nullable();
			$table->text('about')->nullable();
			$table->unsignedTinyInteger('mf_type')->nullable();
			$table->unsignedTinyInteger('komis')->nullable();
			$table->unsignedTinyInteger('sclad')->nullable();
			$table->unsignedTinyInteger('class')->nullable();
			$table->unsignedSmallInteger('mz_1')->nullable();
			$table->unsignedSmallInteger('mz_2')->nullable();
			$table->tinyInteger('otravl')->nullable();
			$table->unsignedTinyInteger('use_mana')->nullable();
			$table->unsignedSmallInteger('magic')->nullable();
			$table->timestamps();
			$table->index(['user_id'], 'user');
			$table->index(['tip'], 'tip');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('users_items');
	}
};
