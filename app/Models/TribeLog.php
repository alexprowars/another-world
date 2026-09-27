<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TribeLog extends Model
{
	/** @return BelongsTo<Tribe, $this> */
	public function tribe(): BelongsTo
	{
		return $this->belongsTo(Tribe::class);
	}

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class)->withTrashed();
	}

	/** @return BelongsTo<User, $this> */
	public function targetUser(): BelongsTo
	{
		return $this->belongsTo(User::class)->withTrashed();
	}
}
