<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('email', 50)->unique();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password')->nullable();
			$table->string('name', 100)->nullable();
			$table->timestamp('blocked_at')->nullable();
			$table->timestamp('online')->nullable();
			$table->bigInteger('ip')->nullable();
			$table->enum('gender', ['M', 'F'])->nullable();
			$table->char('locale', 2)->default('ru');
			$table->unsignedInteger('exp')->default(0);
			$table->unsignedTinyInteger('level')->default(0);
			$table->unsignedTinyInteger('up')->default(0);
			$table->unsignedTinyInteger('updates')->default(3);
			$table->float('moneys', 2)->default(0);
			$table->float('credits', 2)->default(0);
			$table->unsignedSmallInteger('wins')->default(0);
			$table->unsignedSmallInteger('losses')->default(0);
			$table->unsignedSmallInteger('draws')->default(0);
			$table->unsignedSmallInteger('room')->default(0);
			$table->unsignedSmallInteger('rank')->nullable();
			$table->smallInteger('s_strength')->default(3);
			$table->smallInteger('s_dexterity')->default(3);
			$table->smallInteger('s_agility')->default(3);
			$table->smallInteger('s_vitality')->default(3);
			$table->smallInteger('s_power')->default(1);
			$table->smallInteger('s_intelligence')->default(0);
			$table->string('image', 50)->nullable();
			$table->unsignedSmallInteger('profession')->nullable();
			$table->decimal('hp_now', 12, 4)->default(15);
			$table->unsignedInteger('hp_max')->default(15);
			$table->decimal('energy_now', 12, 4)->default(0);
			$table->unsignedInteger('energy_max')->default(0);
			$table->decimal('ustal_now', 12, 4)->default(0);
			$table->unsignedInteger('ustal_max')->default(0);
			$table->unsignedInteger('rating')->default(0);
			$table->foreignId('tribe_id')->nullable()->constrained('tribes')->nullOnDelete();
			$table->foreignId('battle_id')->nullable()->constrained('battles')->nullOnDelete();
			$table->integer('r_type')->nullable();
			$table->timestamp('r_date')->nullable();
			$table->timestamp('silence')->nullable();
			$table->timestamp('injury')->nullable();
			$table->tinyInteger('injury_type')->nullable();
			$table->timestamp('invisible')->nullable();
			$table->timestamp('vip')->nullable();
			$table->smallInteger('poison')->nullable();
			$table->rememberToken();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('users');
	}
};
