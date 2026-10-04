<?php

namespace App\Http\Resources;

use App\Models\UserFriend;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserFriend $resource
 */
class UserFriendResource extends JsonResource
{
	public function toArray($request): array
	{
		$entry = $this->resource;
		$friend = $entry->friend;

		return [
			'id' => $entry->id,
			'is_ignored' => $entry->is_ignored,
			'user' => [
				...$friend->only(['id', 'name', 'level', 'rank', 'location']),
				'location_name' => $friend->currentLocation()->name(),
				'tribe' => $friend->tribe?->only(['id', 'name']),
				'is_online' => $friend->rank !== 100 && ($friend->isBot() || $friend->online?->greaterThanOrEqualTo(now()->subMinutes(3))),
			],
		];
	}
}
