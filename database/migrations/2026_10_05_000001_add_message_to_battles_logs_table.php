<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('battles_logs', function (Blueprint $table) {
			$table->text('message')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('battles_logs', function (Blueprint $table) {
			$table->dropColumn('message');
		});
	}
};
