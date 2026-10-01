<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class BattleUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
	use Dispatchable;

	/** @param list<int> $userIds */
	public function __construct(public int $battleId, private array $userIds)
	{
	}

	/** @return list<PrivateChannel> */
	public function broadcastOn(): array
	{
		return array_map(fn (int $userId) => new PrivateChannel('user.' . $userId), $this->userIds);
	}

	/** @return array{battleId: int} */
	public function broadcastWith(): array
	{
		return ['battleId' => $this->battleId];
	}
}
