<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattleMember extends Model
{
	protected $table = 'battles_members';

	public function battle(): BelongsTo
	{
		return $this->belongsTo(Battle::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
