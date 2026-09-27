<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketItem extends Model
{
	/** @return BelongsTo<UserItem, $this> */
	public function item(): BelongsTo
	{
		return $this->belongsTo(UserItem::class, 'user_item_id');
	}

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}