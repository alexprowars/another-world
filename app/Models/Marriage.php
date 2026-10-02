<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marriage extends Model
{
	protected $casts = [
		'married_at' => 'immutable_datetime',
		'divorced_at' => 'immutable_datetime',
	];

	/** @return BelongsTo<User, $this> */
	public function husband(): BelongsTo
	{
		return $this->belongsTo(User::class, 'husband_id')->withTrashed();
	}

	/** @return BelongsTo<User, $this> */
	public function wife(): BelongsTo
	{
		return $this->belongsTo(User::class, 'wife_id')->withTrashed();
	}

	/** @return BelongsTo<User, $this> */
	public function priest(): BelongsTo
	{
		return $this->belongsTo(User::class, 'priest_id')->withTrashed();
	}

	/** @param Builder<Marriage> $query */
	public function scopeActive(Builder $query): void
	{
		$query->whereNull('divorced_at');
	}

	/** @param Builder<Marriage> $query */
	public function scopeForUser(Builder $query, User $user): void
	{
		$query->where(function (Builder $query) use ($user) {
			$query->where('husband_id', $user->id)
				->orWhere('wife_id', $user->id);
		});
	}
}
