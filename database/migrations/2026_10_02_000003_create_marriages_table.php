<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('marriages', function (Blueprint $table) {
			$table->id();
			$table->foreignId('husband_id')->constrained('users');
			$table->foreignId('wife_id')->constrained('users');
			$table->foreignId('priest_id')->constrained('users');
			$table->timestamp('married_at')->index();
			$table->timestamp('divorced_at')->nullable();
			$table->foreignId('divorce_priest_id')->nullable()->constrained('users');
			$table->timestamps();
			$table->index(['husband_id', 'divorced_at']);
			$table->index(['wife_id', 'divorced_at']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('marriages');
	}
};
