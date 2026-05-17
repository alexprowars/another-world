<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAbility extends Model
{
	public $timestamps = false;
	protected $table = 'users_abilities';

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
