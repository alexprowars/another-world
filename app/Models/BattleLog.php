<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattleLog extends Model
{
	public $timestamps = false;
	protected $table = 'battles_logs';

	protected function casts(): array
	{
		return [
			'date' => 'immutable_datetime',
			'hit' => 'array',
			'block' => 'array',
			'enemy_block' => 'array',
		];
	}

	/** @return BelongsTo<Battle, $this> */
	public function battle(): BelongsTo
	{
		return $this->belongsTo(Battle::class);
	}

	/** @return BelongsTo<BattleMember, $this> */
	public function member(): BelongsTo
	{
		return $this->belongsTo(BattleMember::class, 'member_id');
	}

	/** @return BelongsTo<BattleMember, $this> */
	public function enemy(): BelongsTo
	{
		return $this->belongsTo(BattleMember::class, 'enemy_id');
	}
}
