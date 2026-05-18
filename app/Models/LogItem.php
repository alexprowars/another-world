<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogItem extends Model
{
	public $timestamps = false;
	protected $table = 'logs_items';

	protected $casts = [
		'date' => 'immutable_datetime',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
