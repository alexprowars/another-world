<?php

namespace App\Models;

use App\Engine\Battle\Enums\BattleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattleLog extends Model
{
	use MassPrunable;

	public $timestamps = false;
	protected $table = 'battles_logs';

	protected $casts = [
		'date' => 'immutable_datetime',
		'hit' => 'array',
		'block' => 'array',
		'enemy_block' => 'array',
	];

	/** @param Builder<static> $query */
	public function scopeVisible(Builder $query): void
	{
		$query->where(function (Builder $query) {
			$query->where('comment_id', '>', 0)
				->orWhereNotNull('message');
		});
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

	/**
	 * @return Builder<static>
	 */
	public function prunable(): Builder
	{
		return static::query()
			->where('date', '<', now()->subDays(90))
			->whereHas('battle', fn (Builder $query) => $query->where('status', BattleStatus::FINISHED));
	}
}
