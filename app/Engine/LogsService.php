<?php

namespace App\Engine;

use App\Models\LogItem;
use App\Models\User;

class LogsService
{
	public static function addItemLog(User|int $user, string $action, string $item, ?string $place = null)
	{
		LogItem::create([
			'user_id' => $user instanceof User ? $user->id : $user,
			'action'	=> $action,
			'item'		=> $item,
			'date'		=> now(),
			'place'		=> $place,
		]);
	}
}
