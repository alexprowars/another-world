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
			$table->timestamp('blocked_at')->nullable();
			$table->timestamp('prison')->nullable()->index();
			$table->string('prison_reason', 255)->nullable();
			$table->timestamp('online')->nullable();
			$table->ipAddress('ip')->nullable();
			$table->enum('gender', ['M', 'F'])->nullable();
			$table->char('locale', 2)->default('ru');
			$table->string('name', 100)->nullable();
			$table->string('city')->nullable();
			$table->text('about')->nullable();
			$table->json('options')->nullable();
			$table->unsignedInteger('exp')->default(0);
			$table->unsignedTinyInteger('level')->default(0);
			$table->unsignedTinyInteger('up')->default(0);
			$table->unsignedTinyInteger('updates')->default(3);
			$table->decimal('gold', 12, 2)->default(0);
			$table->decimal('credits', 12, 2)->default(0);
			$table->unsignedSmallInteger('wins')->default(0);
			$table->unsignedSmallInteger('losses')->default(0);
			$table->unsignedSmallInteger('draws')->default(0);
			$table->unsignedSmallInteger('room')->default(0);
			$table->unsignedSmallInteger('rank')->nullable();
			$table->boolean('is_clone')->default(false);
			$table->smallInteger('strength')->default(3);
			$table->smallInteger('dexterity')->default(3);
			$table->smallInteger('agility')->default(3);
			$table->smallInteger('vitality')->default(3);
			$table->smallInteger('magic')->default(1);
			$table->smallInteger('intelligence')->default(0);
			$table->string('image', 50)->nullable();
			$table->unsignedSmallInteger('profession')->nullable();
			$table->decimal('hp_now', 12, 4)->default(15);
			$table->unsignedInteger('hp_max')->default(15);
			$table->decimal('energy_now', 12, 4)->default(0);
			$table->unsignedInteger('energy_max')->default(0);
			$table->decimal('stamina_now', 12, 4)->default(0);
			$table->unsignedInteger('stamina_max')->default(0);
			$table->unsignedInteger('rating')->default(0);
			$table->foreignId('tribe_id')->nullable();
			$table->unsignedTinyInteger('tribe_rank')->default(0);
			$table->timestamp('inquisitor_check')->nullable();
			$table->foreignId('battle_id')->nullable();
			$table->integer('r_type')->nullable();
			$table->timestamp('r_date')->nullable();
			$table->timestamp('silence')->nullable();
			$table->timestamp('injury')->nullable();
			$table->tinyInteger('injury_type')->nullable();
			$table->tinyInteger('tutorial')->default(0);
			$table->timestamp('invisible')->nullable();
			$table->timestamp('battle_fury')->nullable();
			$table->timestamp('magic_protection')->nullable();
			$table->timestamp('attack_protection')->nullable();
			$table->timestamp('vampire_protection')->nullable();
			$table->unsignedTinyInteger('magic_resistance')->default(0);
			$table->unsignedSmallInteger('aura_duration_bonus')->default(0);
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
