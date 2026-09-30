<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Locale;
use App\Models\Effect;
use App\Models\Referal;
use App\Models\User;
use App\Notifications\UserRegistrationNotification;
use App\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
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

		if (Session::has('ref')) {
			$refer = User::query()->whereKeyNot($user)
				->findOne((int) Session::get('ref'));

			if ($refer) {
				Referal::insert([
					'referal_id' => $user->id,
					'user_id' => $refer->id,
				]);
			}
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

	public static function upgradeStat(User $user, string $stat): void
	{
		if (!in_array($stat, Vars::getStats(), true)) {
			throw new Exception('Неизвестная характеристика');
		}

		DB::transaction(function () use ($user, $stat) {
			$user->refreshForUpdate();

			if ($user->updates <= 0) {
				throw new Exception('У Вас нет свободных увеличений!');
			}

			$user->updates--;
			$user->{'s_' . $stat}++;
			$user->save();
		});
	}

	public static function activateAbility(User $user, int $abilityId): void
	{
		$priem_full = require resource_path('data/battle.php');

		$ability = $priem_full[$abilityId] ?? null;

		if ($ability === null) {
			throw new Exception('Такого приёма не существует');
		}

		if ($user->level < $ability['level']) {
			throw new Exception('Уровень слишком мал!');
		}

		$active = $user->abilities()
			->pluck('ability', 'slot');

		$slot = 1;

		for ($i = 1; $i <= 10; $i++) {
			if (!isset($active[$i])) {
				$slot = $i;
				break;
			}
		}

		$user->abilities()->updateOrCreate(['slot' => $slot], [
			'ability' => $abilityId,
		]);
	}

	public static function deactivateAbility(User $user, int $slot): void
	{
		if ($slot > 10) {
			throw new Exception('Неправильный ввод данных');
		}

		$user->abilities()->where('slot', $slot)->delete();
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
		$a = $user->strength + $user->agility + $user->dexterity + $user->vitality + $user->intelligence + $user->magic - 13;
		$b = round($user->wins / ($user->losses + $user->wins + 0.000001), 2);

		return (int) round(((($user->rating / 1000) + ($a / 10)) * $b) + ($user->level / 2), 2);
	}

	public static function getCuredHealth(User $user): float
	{
		$duration = self::getHealthRegenerationTime($user);

		if ($duration === null || $user->r_type == 2 || $user->hp_now >= $user->hp_max) {
			return 0;
		}

		$seconds = max(0, (int) $user->online->diffInSeconds());

		$result = round($user->hp_max * ($seconds / $duration), 4);

		return min($user->hp_max - $user->hp_now, $result);
	}

	public static function getHealthRegenerationTime(User $user): ?int
	{
		if ($user->battle_id || $user->hp_max <= 0) {
			return null;
		}

		if ($user->r_type == 2) {
			return $user->vitality > 0 && $user->r_date?->isFuture()
				? self::getHospitalHealingTime($user)
				: null;
		}

		return $user->online ? 600 : null;
	}

	public static function getHospitalHealingTime(User $user): int
	{
		return $user->level < 4 ? 180 : 360;
	}

	public static function calculateStats(User $user, bool $persist = true): void
	{
		//$user['hp'] = 0;
		//$user['energy'] = 0;

		// Положительные и отрицательные эффекты на персонаже (элики, ауры, проклятья)
		$effects = $user->effects()
			->whereFuture('date')
			->get();

		/** @var Effect $effect */
		foreach ($effects as $effect) {
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
		}
		// Конец эффектов

		foreach (Vars::getStats() as $stat) {
			if ($user->{$stat} < 0) {
				$user->{$stat} = 0;
			}
		}

		// HP, Energy, Stamina
		$user->hp_max = $user->vitality * 5 + $user->hp;
		$user->hp_now = min($user->hp_now, $user->hp_max);

		$user->energy_max = (int) ceil($user->magic * 5 + $user->energy);
		$user->energy_now = min($user->energy_now, $user->energy_max);

		$user->stamina_max = (int) max(0, ($user->vitality + $effects->sum('battery')) * 20);
		$user->stamina_now = min($user->stamina_now, $user->stamina_max);

		// Модификаторы зависят от характеристик после применения зелий и штрафов.
		$user->krit += $user->dexterity * 5;
		$user->unkrit += $user->dexterity * 5;
		$user->uv += $user->agility * 5;
		$user->unuv += $user->agility * 5;

		if ($persist) {
			$user->save();
		}
	}

	public static function calculateWearsStats(User $user, bool $persist = true): void
	{
		$slot = $user->getSlot();

		$wears = $slot->getItems();

		foreach ($wears as $object) {
			if ($object->life?->isPast()) {
				if ($persist) {
					InventoryService::unsetObject($user, $object->onset);
				}

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
