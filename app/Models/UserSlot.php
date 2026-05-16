<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class UserSlot extends Model
{
	protected $table = 'users_slots';
	protected $guarded = [];

	public $timestamps = false;

	public const int MAX_SLOTS = 22;

	protected static function booted(): void
	{
		self::saved(function (self $slot) {
			$slot->clearCache();
		});
	}

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return Collection<int, UserItem>
	 */
	public function getItems(): Collection
	{
		return Cache::remember('user:' . $this->user->id . ':slot', 600, function () {
			$items = $this->getItemsId();

			if (!empty($items)) {
				return $this->user->items()
					->whereIn('id', $items)
					->get();
			}

			return collect();
		});
	}

	public function getItemsId(): array
	{
		$result = [];

		for ($i = 1; $i <= self::MAX_SLOTS; $i++) {
			if ($this->{'i' . $i} > 0) {
				$result[] = $this->{'i' . $i};
			}
		}

		return array_unique($result);
	}

	public function clearCache()
	{
		Cache::forget('user:' . $this->user_id . ':slot');
	}
}
