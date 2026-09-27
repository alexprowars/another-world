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
		'from' => 'integer',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	/** @return BelongsTo<Item, $this> */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	/** @return MorphTo<Model, $this> */
	public function sender(): MorphTo
	{
		return $this->morphTo();
	}
}
