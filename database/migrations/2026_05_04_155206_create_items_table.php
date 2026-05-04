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
			$table->decimal('price', 12)->default(0);
			$table->decimal('credits', 12)->default(0);
			$table->unsignedTinyInteger('type')->index();
			$table->unsignedSmallInteger('slot1')->nullable();
			$table->unsignedSmallInteger('slot2')->nullable();
			$table->unsignedSmallInteger('min_level')->default(0);
			$table->unsignedSmallInteger('min_strength')->default(0);
			$table->unsignedSmallInteger('min_dex')->default(0);
			$table->unsignedSmallInteger('min_agility')->default(0);
			$table->unsignedSmallInteger('min_vitality')->default(0);
			$table->unsignedSmallInteger('min_razum')->default(0);
			$table->unsignedTinyInteger('min_proff')->default(0);
			$table->smallInteger('min')->default(0);
			$table->smallInteger('max')->default(0);
			$table->smallInteger('hp')->default(0);
			$table->smallInteger('energy')->default(0);
			$table->smallInteger('br1')->default(0);
			$table->smallInteger('br2')->default(0);
			$table->smallInteger('br3')->default(0);
			$table->smallInteger('br4')->default(0);
			$table->smallInteger('br5')->default(0);
			$table->smallInteger('strength')->default(0);
			$table->smallInteger('dex')->default(0);
			$table->smallInteger('agility')->default(0);
			$table->smallInteger('vitality')->default(0);
			$table->smallInteger('razum')->default(0);
			$table->smallInteger('krit')->default(0);
			$table->smallInteger('mkrit')->default(0);
			$table->smallInteger('unkrit')->default(0);
			$table->smallInteger('uv')->default(0);
			$table->smallInteger('unuv')->default(0);
			$table->smallInteger('mblock')->default(0);
			$table->smallInteger('kbr')->default(0);
			$table->smallInteger('pbr')->default(0);
			$table->smallInteger('metk')->default(0);
			$table->unsignedSmallInteger('iznos')->default(0);
			$table->text('about')->nullable();
			$table->unsignedTinyInteger('art')->default(0);
			$table->unsignedInteger('life')->default(0);
			$table->unsignedTinyInteger('real_price')->default(0);
			$table->unsignedTinyInteger('craft')->default(0);
			$table->unsignedTinyInteger('class')->default(0);
			$table->smallInteger('pblock')->default(0);
			$table->smallInteger('mz')->default(0);
			$table->foreignId('tribe_id')->nullable()->constrained('tribes')->restrictOnDelete();
			$table->unsignedTinyInteger('sclon')->default(0);
			$table->tinyInteger('otravl')->default(0);
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
