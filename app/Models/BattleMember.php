<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattleMember extends Model
{
	protected $table = 'battles_members';

	protected $casts = [
		'finished_at' => 'immutable_datetime',
		'died_at' => 'immutable_datetime'
	];

	/** @return BelongsTo<Battle, $this> */
	public function battle(): BelongsTo
	{
		return $this->belongsTo(Battle::class);
	}

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
