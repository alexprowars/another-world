<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserGift extends Model
{
	public $timestamps = false;
	protected $table = 'users_gifts';

	protected $casts = [
		'date' => 'immutable_datetime',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function sender(): MorphTo
	{
		return $this->morphTo();
	}
}
