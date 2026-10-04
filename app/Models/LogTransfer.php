<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogTransfer extends Model
{
	use MassPrunable;

	protected $table = 'logs_transfers';

	protected $casts = [
		'gold' => 'decimal:2',
	];

	/** @return BelongsTo<User, $this> */
	public function sender(): BelongsTo
	{
		return $this->belongsTo(User::class, 'sender_id');
	}

	/** @return BelongsTo<User, $this> */
	public function recipient(): BelongsTo
	{
		return $this->belongsTo(User::class, 'recipient_id');
	}

	/** @return BelongsTo<UserItem, $this> */
	public function item(): BelongsTo
	{
		return $this->belongsTo(UserItem::class, 'user_item_id');
	}

	/**
	 * @return Builder<static>
	 */
	public function prunable(): Builder
	{
		return static::query()->where('created_at', '<', now()->subDays(180));
	}
}
