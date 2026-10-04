<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatMessage extends Model
{
	use MassPrunable;

	public const string KIND_PLAYER = 'player';
	public const string KIND_SYSTEM = 'system';
	public const string VISIBILITY_PUBLIC = 'public';
	public const string VISIBILITY_PRIVATE = 'private';

	public $timestamps = false;

	protected $casts = [
		'created_at' => 'immutable_datetime',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class)->withTrashed();
	}

	/** @return BelongsToMany<User, $this, ChatRecipient> */
	public function recipients(): BelongsToMany
	{
		return $this->belongsToMany(User::class, ChatRecipient::class)->withTrashed();
	}

	/** @param Builder<ChatMessage> $query */
	public function scopeVisibleTo(Builder $query, User $user): void
	{
		$query->where(function (Builder $query) use ($user) {
			$query->where('visibility', self::VISIBILITY_PUBLIC)
				->orWhere(function (Builder $query) use ($user) {
					$query->where('visibility', self::VISIBILITY_PRIVATE)
						->where(function (Builder $query) use ($user) {
							$query->where('user_id', $user->id)
								->orWhereExists(
									ChatRecipient::query()
										->selectRaw('1')
										->whereColumn('chat_message_id', $query->qualifyColumn('id'))
										->where('user_id', $user->id)
								);
						});
				});
		});
	}

	/**
	 * @return Builder<static>
	 */
	public function prunable(): Builder
	{
		return static::query()->where('created_at', '<', now()->subDays(30));
	}
}
