<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Locale;
use App\Models\Effect;
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
			'name' => $data['name'] ?? '',
			'ip' => Request::ip(),
			'online' => now(),
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

	public static function checkRoom(User $user, int $room)
	{
		if ($room != $user->room) {
			$user->room = $room;
			$user->save();
		}
	}

	public static function getUserRaiting(User $user): int
	{
		// Вычисление рейтинга крутизны (цена вещей, статы, процент побед)
		$a = $user->strength + $user->agility + $user->dexterity + $user->vitality + $user->intelligence + $user->battery + $user->power - 14;
		$b = round($user->wins / ($user->losses + $user->wins + 0.000001), 2);

		return round(((($user->rating / 1000) + ($a / 10)) * $b) + ($user->level / 2), 2);
	}

	public static function getCuredHealth(User $user): float
	{
		$result = 0;

		if (!$user->battle_id && $user->r_type != 2 && ($user->hp_now < $user->hp_max) && $user->hp_max != 0 && $user->online) {
			$result = round($user->hp_max * (((int) $user->online->diffInSeconds()) / 600), 4);

			if (($user->hp_now + $result) > $user->hp_max) {
				$result = $user->hp_max - $user->hp_now;
			}
		}

		return $result;
	}

	public static function calculateStats(User $user)
	{
		if ($user->provin == 1) {
			$user->battery = 1;
		}

		//$user['hp'] = 0;
		//$user['energy'] = 0;

		// МФ
		$user->krit		+= ($user->dexterity * 5);
		$user->unkrit	+= ($user->dexterity * 5);
		$user->uv		+= ($user->agility * 5);
		$user->unuv		+= ($user->agility * 5);

		// Положительные и отрицательные эффекты на персонаже (элики, ауры, проклятья)
		$effects = $user->effects()
			->whereFuture('date')
			->get();

		/** @var Effect $effect */
		foreach ($effects as $effect) {
			//$user->auraInfo[] = $effect;

			foreach (Vars::getStats() as $stat) {
				if (isset($effect[$stat])) {
					$user->{$stat} += $effect[$stat];
				}
			}

			$user->armor1 += $effect->armor1 ?? 0;
			$user->armor2 += $effect->armor2 ?? 0;
			$user->armor3 += $effect->armor3 ?? 0;
			$user->armor4 += $effect->armor4 ?? 0;
			$user->armor5 += $effect->armor5 ?? 0;
			$user->min += $effect->min ?? 0;
			$user->max += $effect->max ?? 0;

			$user->effects++;
		}
		// Конец эффектов

		foreach (Vars::getStats() as $stat) {
			if ($user->{$stat} < 0) {
				$user->{$stat} = 0;
			}
		}

		// HP, Energy, Battery
		$user->hp_max = $user->vitality * 5 + $user->hp;
		$user->hp_now = min($user->hp_now, $user->hp_max);

		$user->energy_max = ceil($user->magic * 5 + $user->energy);
		$user->energy_now = min($user->energy_now, $user->energy_max);

		$user->ustal_max = $user->battery * 20;
		$user->ustal_now = min($user->ustal_now, $user->ustal_max);

		$user->save();
	}

	public static function calculateWearsStats(User $user)
	{
		$slot = $user->getSlot();

		$wears = $slot->getItems();

		foreach ($wears as $object) {
			if ($object->life?->isPast()) {
				InventoryService::unsetObject($user, $object->onset);

				continue;
			}

			foreach (Vars::getStats() as $stat) {
				if (isset($object->{$stat})) {
					$user->{$stat} += $object->{$stat};
				}
			}

			$user->hp		+= $object->hp;
			$user->energy	+= $object->energy;

			$user->armor1	+= $object->armor1;
			$user->armor2	+= $object->armor2;
			$user->armor3	+= $object->armor3;
			$user->armor4	+= $object->armor4;
			$user->armor5	+= $object->armor5;

			$user->krit		+= $object->krit;
			$user->mkrit	+= $object->mkrit;
			$user->unkrit	+= $object->unkrit;
			$user->uv		+= $object->uv;
			$user->unuv		+= $object->unuv;

			$user->pblock	+= $object->pblock;
			$user->mblock	+= $object->mblock;
			$user->pbr		+= $object->pbr;
			$user->kbr		+= $object->kbr;

			$user->min		+= $object->min;
			$user->max		+= $object->max;

			// Для вычисления рейтинга (стоимость вещи)
			if ($object->price) {
				$user->rating += $object->price;
			}
		}
	}
}
