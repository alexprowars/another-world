<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryTicket extends Model
{
	protected $casts = [
		'number' => 'integer',
	];

	/** @return BelongsTo<LotteryDraw, $this> */
	public function draw(): BelongsTo
	{
		return $this->belongsTo(LotteryDraw::class, 'lottery_draw_id');
	}

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
