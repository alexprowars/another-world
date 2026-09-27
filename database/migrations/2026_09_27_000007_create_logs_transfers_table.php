<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('logs_transfers', function (Blueprint $table) {
			$table->id();
			$table->foreignId('sender_id')->constrained('users');
			$table->foreignId('recipient_id')->constrained('users');
			$table->foreignId('user_item_id')->nullable()->constrained('users_items')->nullOnDelete();
			$table->string('item_title', 150)->nullable();
			$table->decimal('gold', 12, 2)->default(0);
			$table->string('comment')->nullable();
			$table->ipAddress('ip')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('logs_transfers');
	}
};
