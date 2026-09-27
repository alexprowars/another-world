<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Models\User;

class FriendService
{
	public static function add(User $user, string $name, bool $isIgnored): void
	{
		$friend = self::findFriend($name);

		if ($friend->is($user)) {
			throw new Exception('Вы не можете добавить себя в свой список');
		}

		$entry = $user->friends()->firstOrCreate([
			'friend_id' => $friend->id,
		], [
			'is_ignored' => $isIgnored,
		]);

		if (!$entry->wasRecentlyCreated) {
			throw new Exception('Персонаж уже записан в ваш список');
		}
	}

	public static function remove(User $user, string $name): void
	{
		$friend = self::findFriend($name);

		if (!$user->friends()->whereBelongsTo($friend, 'friend')->delete()) {
			throw new Exception('Персонажа нет в вашем списке');
		}
	}

	private static function findFriend(string $name): User
	{
		$friend = User::query()->where('name', $name)->first();

		if (!$friend) {
			throw new Exception('Персонаж не существует');
		}

		return $friend;
	}
}
