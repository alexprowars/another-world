<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Effect extends Model
{
	use MassPrunable;

	public const int AURA = 1;
	public const int POTION = 2;
	public const int INJURY = 3;
	public const int POISON = 4;

	public $timestamps = false;
	protected $table = 'effects';

	protected $casts = [
		'date' => 'immutable_datetime',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return Builder<static>
	 */
	public function prunable(): Builder
	{
		return static::query()->where('date', '<', now()->subWeek());
	}
}
