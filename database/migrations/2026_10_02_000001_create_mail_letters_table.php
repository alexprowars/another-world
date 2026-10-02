<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::create('mail_letters', function (Blueprint $table) {
			$table->id();
			$table->foreignId('sender_id')->constrained('users')->restrictOnDelete();
			$table->foreignId('recipient_id')->constrained('users')->restrictOnDelete();
			$table->string('subject', 100);
			$table->text('body');
			$table->timestamp('read_at')->nullable();
			$table->timestamp('created_at')->useCurrent();
			$table->index(['sender_id', 'id']);
			$table->index(['recipient_id', 'id']);
			$table->index(['recipient_id', 'read_at']);
		});
	}

	public function down()
	{
		Schema::dropIfExists('mail_letters');
	}
};
