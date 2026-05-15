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
			$table->string('code', 50);
			$table->string('title', 150);
			$table->decimal('price', 12)->default(0);
			$table->boolean('artifact')->default(false);
			$table->boolean('second')->default(false);
			$table->unsignedSmallInteger('wearout')->default(0);
			$table->unsignedSmallInteger('wearout_max')->default(0);
			$table->json('requirements')->nullable();
			$table->unsignedTinyInteger('type')->index();
			$table->smallInteger('armor1')->default(0);
			$table->smallInteger('armor2')->default(0);
			$table->smallInteger('armor3')->default(0);
			$table->smallInteger('armor4')->default(0);
			$table->smallInteger('armor5')->default(0);
			$table->smallInteger('min')->default(0);
			$table->smallInteger('max')->default(0);
			$table->smallInteger('hp')->default(0);
			$table->smallInteger('energy')->default(0);
			$table->smallInteger('strength')->default(0);
			$table->smallInteger('dexterity')->default(0);
			$table->smallInteger('agility')->default(0);
			$table->smallInteger('vitality')->default(0);
			$table->smallInteger('intelligence')->default(0);
			$table->smallInteger('krit')->default(0);
			$table->smallInteger('mkrit')->default(0);
			$table->smallInteger('unkrit')->default(0);
			$table->smallInteger('uv')->default(0);
			$table->smallInteger('unuv')->default(0);
			$table->smallInteger('pblock')->default(0);
			$table->tinyInteger('mblock')->default(0);
			$table->smallInteger('pbr')->default(0);
			$table->smallInteger('kbr')->default(0);
			$table->timestamp('life')->nullable();
			$table->unsignedTinyInteger('present')->nullable();
			$table->boolean('bank')->default(false);
			$table->unsignedTinyInteger('onset')->nullable();
			$table->string('about')->nullable();
			$table->string('engraving')->nullable();
			$table->unsignedTinyInteger('mf_type')->nullable();
			$table->boolean('komis')->default(false);
			$table->boolean('sclad')->default(false);
			$table->unsignedTinyInteger('class')->nullable();
			$table->unsignedSmallInteger('mz_1')->nullable();
			$table->unsignedSmallInteger('mz_2')->nullable();
			$table->tinyInteger('poison')->nullable();
			$table->unsignedTinyInteger('use_mana')->nullable();
			$table->unsignedSmallInteger('magic')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('users_items');
	}
};
