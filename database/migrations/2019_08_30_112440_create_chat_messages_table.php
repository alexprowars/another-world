<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::create('chat_messages', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->nullable()->constrained('users')->restrictOnDelete();
			$table->enum('kind', ['player', 'system']);
			$table->enum('visibility', ['public', 'private']);
			$table->string('system_name')->nullable();
			$table->text('body');
			$table->string('redirect')->nullable();
			$table->timestamp('created_at')->useCurrent();
			$table->index(['visibility', 'id']);
			$table->index(['user_id', 'id']);
		});

		Schema::create('chat_recipients', function (Blueprint $table) {
			$table->foreignId('chat_message_id')->constrained()->cascadeOnDelete();
			$table->foreignId('user_id')->constrained()->restrictOnDelete();
			$table->primary(['chat_message_id', 'user_id']);
			$table->index(['user_id', 'chat_message_id']);
		});
	}

	public function down()
	{
		Schema::dropIfExists('chat_recipients');
		Schema::dropIfExists('chat_messages');
	}
};
