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
			$table->string('password', 100)->nullable();
			$table->string('nickname', 100)->nullable();
			$table->timestamp('blocked_at')->nullable();
			$table->timestamp('onlinetime')->nullable();
			$table->bigInteger('ip')->nullable();
			$table->enum('gender', ['M', 'F'])->nullable();
			$table->char('locale', 2)->default('en');
			$table->unsignedInteger('experience')->default(0);
			$table->unsignedTinyInteger('level')->default(0);
			$table->unsignedTinyInteger('up')->default(0);
			$table->float('moneys', 2)->default(0);
			$table->float('credits', 2)->default(0);
			$table->unsignedSmallInteger('wins')->default(0);
			$table->unsignedSmallInteger('losses')->default(0);
			$table->unsignedSmallInteger('draws')->default(0);
			$table->unsignedSmallInteger('room')->nullable();
			$table->unsignedSmallInteger('rank')->nullable();
			$table->unsignedSmallInteger('s_strength')->default(3);
			$table->unsignedSmallInteger('s_dex')->default(3);
			$table->unsignedSmallInteger('s_agility')->default(3);
			$table->unsignedSmallInteger('s_vitality')->default(3);
			$table->unsignedSmallInteger('s_power')->default(1);
			$table->unsignedSmallInteger('s_razum')->default(0);
			$table->unsignedSmallInteger('s_battery')->default(0);
			$table->unsignedSmallInteger('updates')->default(0);
			$table->unsignedSmallInteger('obraz')->nullable();
			$table->unsignedSmallInteger('profession')->nullable();
			$table->unsignedInteger('hp_now')->default(0);
			$table->unsignedInteger('hp_max')->default(0);
			$table->timestamp('hp_updated')->nullable();
			$table->unsignedInteger('energy_now')->default(0);
			$table->unsignedInteger('energy_max')->default(0);
			$table->timestamp('energy_updated')->nullable();
			$table->unsignedInteger('ustal_now')->default(0);
			$table->unsignedInteger('ustal_max')->default(0);
			$table->timestamp('ustal_updated')->nullable();
			$table->unsignedInteger('rating')->default(0);
			$table->foreignId('tribe_id')->nullable()->constrained('tribes')->nullOnDelete();
			$table->foreignId('battle_id')->nullable();
			$table->integer('r_type')->nullable();
			$table->timestamp('r_date')->nullable();
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
