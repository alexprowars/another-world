<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatPublicMessage implements ShouldBroadcast
{
	use Dispatchable;
	use SerializesModels;

	public function __construct(public array $message)
	{
	}

	public function broadcastOn()
	{
		return new PrivateChannel('chat');
	}
}
