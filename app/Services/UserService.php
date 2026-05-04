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
}
