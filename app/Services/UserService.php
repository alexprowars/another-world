<?php

namespace App\Services;

use App\Engine\Battle\Abilities\AbilityRegistry;
use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Locale;
use App\Models\Effect;
use App\Models\Referal;
use App\Models\User;
use App\Notifications\UserRegistrationNotification;
use App\Settings;
use Carbon\CarbonImmutable;
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
		$gender = $data['gender'] ?? null;

		if ($gender !== null && !in_array($gender, ['M', 'F'], true)) {
			throw new Exception('Выберите пол персонажа из списка.');
		}

		if (empty($data['password'])) {
			$data['password'] = Str::random(10);
		}

		$user = User::create([
			'email' => $data['email'] ?? '',
			'password' => Hash::make($data['password']),
			'name' => $data['name'] ?? '',
			'gender' => $gender,
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
			$user->{$stat}++;
			$user->save();
		});
	}

	public static function activateAbility(User $user, int $abilityId): void
	{
		$ability = AbilityRegistry::find($abilityId);

		if ($ability === null) {
			throw new Exception('Такого приёма не существует');
		}

		DB::transaction(function () use ($user, $ability, $abilityId) {
			$user->refreshForUpdate();

			if ($user->level < $ability->level) {
				throw new Exception('Уровень слишком мал!');
			}

			$active = $user->abilities()
				->pluck('ability', 'slot');

			$slot = null;

			for ($i = 1; $i <= 10; $i++) {
				if (!isset($active[$i])) {
					$slot = $i;
					break;
				}
			}

			if ($slot === null) {
				throw new Exception('Все 10 слотов приёмов заняты. Сначала уберите один из выбранных приёмов.');
			}

			$user->abilities()->create([
				'slot' => $slot,
				'ability' => $abilityId,
			]);
		}, 3);
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
		$combatStats = $user->getCombatStats();

		// Вычисление рейтинга крутизны (цена вещей, статы, процент побед)
		$a = $combatStats->strength
			+ $combatStats->agility
			+ $combatStats->dexterity
			+ $combatStats->vitality
			+ $combatStats->intelligence
			+ $combatStats->magic
			- 13;

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
			return $user->getCombatStats()->vitality > 0 && $user->r_date?->isFuture()
				? self::getHospitalHealingTime($user)
				: null;
		}

		return $user->online ? 600 : null;
	}

	public static function getHospitalHealingTime(User $user): int
	{
		return $user->level < 4 ? 180 : 360;
	}

	public static function calculateStats(User $user, CarbonImmutable $time, bool $persist = true): void
	{
		$combatStats = $user->getCombatStats();

		// Положительные и отрицательные эффекты на персонаже (элики, ауры, проклятья)
		$effects = $user->effects()
			->where('date', '>', $time)
			->get();

		/** @var Effect $effect */
		foreach ($effects as $effect) {
			foreach (Vars::getStats() as $stat) {
				if (isset($effect[$stat])) {
					$combatStats->{$stat} += $effect[$stat];
				}
			}

			$combatStats->armor1 += $effect->armor1 ?? 0;
			$combatStats->armor2 += $effect->armor2 ?? 0;
			$combatStats->armor3 += $effect->armor3 ?? 0;
			$combatStats->armor4 += $effect->armor4 ?? 0;
			$combatStats->armor5 += $effect->armor5 ?? 0;
			$combatStats->min += $effect->min ?? 0;
			$combatStats->max += $effect->max ?? 0;
		}
		// Конец эффектов

		foreach (Vars::getStats() as $stat) {
			if ($combatStats->{$stat} < 0) {
				$combatStats->{$stat} = 0;
			}
		}

		// HP, Energy, Stamina
		$user->hp_max = $combatStats->vitality * 5 + $user->hp;
		$user->hp_now = min($user->hp_now, $user->hp_max);

		$user->energy_max = (int) ceil($combatStats->magic * 5 + $user->energy);
		$user->energy_now = min($user->energy_now, $user->energy_max);

		$user->stamina_max = (int) max(0, ($combatStats->vitality + $effects->sum('battery')) * 20);
		$user->stamina_now = min($user->stamina_now, $user->stamina_max);

		// Модификаторы зависят от характеристик после применения зелий и штрафов.
		$combatStats->addStatModifiers();

		if ($persist) {
			$user->save();
		}
	}

	public static function calculateWearsStats(User $user, CarbonImmutable $time, bool $persist = true): void
	{
		$combatStats = $user->getCombatStats();

		$slot = $user->getSlot();

		$wears = $slot->getItems();

		foreach ($wears as $object) {
			if ($object->life?->lessThan($time)) {
				if ($persist) {
					InventoryService::unsetObject($user, $object->onset);
				}

				continue;
			}

			foreach (Vars::getStats() as $stat) {
				if (isset($object->{$stat})) {
					$combatStats->{$stat} += $object->{$stat};
				}
			}

			$user->hp		+= $object->hp;
			$user->energy	+= $object->energy;

			$combatStats->armor1 += $object->armor1;
			$combatStats->armor2 += $object->armor2;
			$combatStats->armor3 += $object->armor3;
			$combatStats->armor4 += $object->armor4;
			$combatStats->armor5 += $object->armor5;

			$combatStats->krit += $object->krit;
			$combatStats->mkrit += $object->mkrit;
			$combatStats->unkrit += $object->unkrit;
			$combatStats->uv += $object->uv;
			$combatStats->unuv += $object->unuv;

			$combatStats->pblock += $object->pblock;
			$combatStats->mblock += $object->mblock;
			$combatStats->pbr += $object->pbr;
			$combatStats->kbr += $object->kbr;

			$combatStats->min += $object->min;
			$combatStats->max += $object->max;

			// Для вычисления рейтинга (стоимость вещи)
			if ($object->price) {
				$user->rating += $object->price;
			}
		}
	}
}
