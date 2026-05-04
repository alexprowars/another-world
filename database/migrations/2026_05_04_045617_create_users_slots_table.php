<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('users_slots', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
			$table->unsignedBigInteger('i1')->nullable();
			$table->unsignedBigInteger('i2')->nullable();
			$table->unsignedBigInteger('i3')->nullable();
			$table->unsignedBigInteger('i4')->nullable();
			$table->unsignedBigInteger('i5')->nullable();
			$table->unsignedBigInteger('i6')->nullable();
			$table->unsignedBigInteger('i7')->nullable();
			$table->unsignedBigInteger('i8')->nullable();
			$table->unsignedBigInteger('i9')->nullable();
			$table->unsignedBigInteger('i10')->nullable();
			$table->unsignedBigInteger('i11')->nullable();
			$table->unsignedBigInteger('i12')->nullable();
			$table->unsignedBigInteger('i13')->nullable();
			$table->unsignedBigInteger('i14')->nullable();
			$table->unsignedBigInteger('i15')->nullable();
			$table->unsignedBigInteger('i16')->nullable();
			$table->unsignedBigInteger('i17')->nullable();
			$table->unsignedBigInteger('i18')->nullable();
			$table->unsignedBigInteger('i19')->nullable();
			$table->unsignedBigInteger('i20')->nullable();
			$table->unsignedBigInteger('i21')->nullable();
			$table->unsignedBigInteger('i22')->nullable();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('users_slots');
	}
};
