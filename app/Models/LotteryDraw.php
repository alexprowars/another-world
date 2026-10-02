<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LotteryDraw extends Model
{
	protected $casts = [
		'draws_at' => 'immutable_datetime',
		'drawn_at' => 'immutable_datetime',
		'ticket_count' => 'integer',
		'winning_number' => 'integer',
		'prize' => 'float',
	];

	/** @return HasMany<LotteryTicket, $this> */
	public function tickets(): HasMany
	{
		return $this->hasMany(LotteryTicket::class);
	}

	/** @return BelongsTo<User, $this> */
	public function winner(): BelongsTo
	{
		return $this->belongsTo(User::class, 'winner_id');
	}
}
