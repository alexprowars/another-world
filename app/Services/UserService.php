<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Locale;
use App\Models\User;
use App\Notifications\UserRegistrationNotification;
use App\Settings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Throwable;

class UserService
{
	public static function creation(array $data, bool $notify = false): User
	{
		if (empty($data['password'])) {
			$data['password'] = Str::random(10);
		}

		$user = User::create([
			'email' => $data['email'] ?? '',
			'password' => Hash::make($data['password']),
			'nickname' => $data['name'] ?? '',
			'ip' => Request::ip(),
			'onlinetime' => now(),
			'locale' => Locale::getPreferredLocale(),
		]);

		if (!$user->id) {
			throw new Exception('create user error');
		}

		$settings = app(Settings::class);
		$settings->usersTotal++;
		$settings->save();

		if ($notify && !empty($user->email)) {
			try {
				$user->notify(new UserRegistrationNotification($data['password'])->afterCommit());
			} catch (Throwable) {
			}
		}

		return $user;
	}

	public static function calculateRating(User $user): int
	{
		$s_p = 0;
		$s_s = $user->strength + $user->agility + $user->dex + $user->vitality + $user->razum + $user->battery + $user->power + $user->duh - 14;
		$s_i = round($user->wins / ($user->losses + $user->wins + 0.000001), 2);

		return (int) round(((($s_p / 1000) + ($s_s / 10)) * $s_i) + ($user->level / 2), 2);
	}

	public static function regenerationParams(User $user)
	{
		$hp = 0;

		if ($user->battle_id == 0 && $user->r_type != 2 && ($user->hp_now < $user->hp_max) && $user->hp_max > 0) {
			$hp = round($user->hp_max * 0.025, 1);

			if (($stat['hp_now'] + $add_hp) > $stat['hp_maxi']) {
				$hp = $stat['hp_maxi'] - $stat['hp_now'];
			}
		}
	}
}
