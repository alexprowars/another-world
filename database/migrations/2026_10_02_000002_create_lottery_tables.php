<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('lottery_draws', function (Blueprint $table) {
			$table->id();
			$table->timestamp('draws_at')->unique();
			$table->unsignedSmallInteger('ticket_count')->default(0);
			$table->decimal('prize', 12, 2)->default(0);
			$table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
			$table->string('winner_name', 100)->nullable();
			$table->unsignedSmallInteger('winning_number')->nullable();
			$table->timestamp('drawn_at')->nullable();
			$table->timestamps();
			$table->index(['drawn_at', 'draws_at']);
		});

		Schema::create('lottery_tickets', function (Blueprint $table) {
			$table->id();
			$table->foreignId('lottery_draw_id')->constrained()->cascadeOnDelete();
			$table->foreignId('user_id')->constrained();
			$table->unsignedSmallInteger('number');
			$table->timestamps();
			$table->unique(['lottery_draw_id', 'number']);
			$table->index(['lottery_draw_id', 'user_id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('lottery_tickets');
		Schema::dropIfExists('lottery_draws');
	}
};
