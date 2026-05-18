<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('items', function (Blueprint $table) {
			$table->id();
			$table->string('code', 50);
			$table->string('title', 150);
			$table->decimal('gold', 12)->default(0);
			$table->decimal('credits', 12)->default(0);
			$table->unsignedTinyInteger('type')->index();
			$table->unsignedSmallInteger('slot1')->nullable();
			$table->unsignedSmallInteger('slot2')->nullable();
			$table->unsignedSmallInteger('req_level')->default(0);
			$table->unsignedSmallInteger('req_strength')->default(0);
			$table->unsignedSmallInteger('req_dexterity')->default(0);
			$table->unsignedSmallInteger('req_agility')->default(0);
			$table->unsignedSmallInteger('req_vitality')->default(0);
			$table->unsignedSmallInteger('req_intelligence')->default(0);
			$table->unsignedTinyInteger('req_profession')->default(0);
			$table->smallInteger('min')->default(0);
			$table->smallInteger('max')->default(0);
			$table->smallInteger('hp')->default(0);
			$table->smallInteger('energy')->default(0);
			$table->smallInteger('armor1')->default(0);
			$table->smallInteger('armor2')->default(0);
			$table->smallInteger('armor3')->default(0);
			$table->smallInteger('armor4')->default(0);
			$table->smallInteger('armor5')->default(0);
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
			$table->smallInteger('mblock')->default(0);
			$table->smallInteger('kbr')->default(0);
			$table->smallInteger('pbr')->default(0);
			$table->smallInteger('metk')->default(0);
			$table->unsignedSmallInteger('wearout')->default(0);
			$table->string('about')->nullable();
			$table->boolean('artifact')->default(false);
			$table->unsignedInteger('life')->default(0);
			$table->unsignedTinyInteger('real_price')->default(0);
			$table->unsignedTinyInteger('craft')->default(0);
			$table->unsignedTinyInteger('class')->default(0);
			$table->smallInteger('pblock')->default(0);
			$table->smallInteger('mz')->default(0);
			$table->foreignId('tribe_id')->nullable()->constrained('tribes')->restrictOnDelete();
			$table->unsignedTinyInteger('sclon')->default(0);
			$table->tinyInteger('poison')->default(0);
			$table->unsignedTinyInteger('use_mana')->default(0);
			$table->unsignedSmallInteger('magic')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('items');
	}
};
