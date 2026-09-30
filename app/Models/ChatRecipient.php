<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatRecipient extends Pivot
{
	public $timestamps = false;

	protected $table = 'chat_recipients';
}
