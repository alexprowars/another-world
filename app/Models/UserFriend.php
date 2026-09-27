<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFriend extends Model
{
	protected $table = 'users_friends';
	public $timestamps = false;

	protected $casts = [
		'is_ignored' => 'boolean',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/** @return BelongsTo<User, $this> */
	public function friend(): BelongsTo
	{
		return $this->belongsTo(User::class, 'friend_id');
	}
}
