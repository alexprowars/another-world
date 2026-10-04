<?php

namespace App\Engine\Services;

use App\Engine\Battle\Enums\BattleStatus;
use App\Engine\Battle\Enums\BattleType;
use App\Exceptions\Exception;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\Level;
use App\Models\User;
use App\Models\UserItem;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Random\Randomizer;

class BattleService
{
	private const array INJURIES = [
		// лёгкие
		1 => [
			0 => ['param' => 'strength', 'name' => 'шишка на лбу'],
			1 => ['param' => 'strength', 'name' => 'ушиб коленки'],
			2 => ['param' => 'agility', 'name' => 'фингал под глазом'],
			3 => ['param' => 'agility', 'name' => 'растяжение руки'],
			4 => ['param' => 'dexterity', 'name' => 'ушиб ВЦ'],
			5 => ['param' => 'dexterity', 'name' => 'шишка на кулаке'],
		],
		// средние
		2 => [
			0 => ['param' => 'strength', 'name' => 'ушиб коленки второй степени'],
			1 => ['param' => 'strength', 'name' => 'растяжение ВЦ'],
			2 => ['param' => 'agility', 'name' => 'выбитый зуб'],
			3 => ['param' => 'agility', 'name' => 'глубокий порез'],
			4 => ['param' => 'dexterity', 'name' => 'перелом ключицы'],
			5 => ['param' => 'dexterity', 'name' => 'отбитые почки'],
		],
		// тяжелые
		3 => [
			0 => ['param' => 'strength', 'name' => 'открытый перелом руки'],
			1 => ['param' => 'strength', 'name' => 'перелом позвоночника'],
			2 => ['param' => 'agility', 'name' => 'открытый перелом ноги'],
			3 => ['param' => 'agility', 'name' => 'разрыв селезёнки'],
			4 => ['param' => 'dexterity', 'name' => 'множественные порезы'],
			5 => ['param' => 'dexterity', 'name' => 'выбитый глаз'],
		],
	];

	public static function fight(User $user, User $enemy, int $type = 1): void
	{
		$enemyId = $enemy->id;
		$enemyBattleId = $enemy->battle_id;

		DB::transaction(function () use ($user, $enemyId, $enemyBattleId, $type) {
			$battle = $enemyBattleId
				? Battle::query()->lockForUpdate()->find($enemyBattleId)
				: null;

			$participants = User::query()
				->whereKey([$user->id, $enemyId])
				->orderBy('id')
				->lockForUpdate()
				->get()
				->keyBy('id');

			$player = $participants->get($user->id);
			$enemy = $participants->get($enemyId);

			if (!$player || !$enemy) {
				throw new Exception('Противник не найден');
			}

			if ($enemy->battle_id !== $enemyBattleId) {
				throw new Exception('Состояние боя изменилось. Обновите данные');
			}

			$enemy->setRelation('battle', $battle);

			self::fightLocked($player, $enemy, $type);
		}, 3);
	}

	private static function fightLocked(User $user, User $enemy, int $type = 1): void
	{
		if ($enemy->is($user)) {
			throw new Exception('Нападение на самого себя - это уже мазохизм...');
		}

		if ($user->battle_id) {
			throw new Exception('Вы уже участвуете в бою');
		}

		if ($type == 2 && !$user->currentLocation()->is('training-arena')) {
			throw new Exception('Начать тренировку можно только в тренировочном зале');
		}

		if ($type == 2 && $enemy->rank != 60) {
			throw new Exception('Персонаж <u>' . $enemy->name . '</u> не является ботом!');
		}

		if ($type == 2 && $enemy->is_clone) {
			throw new Exception('Этот клон доступен только в вызвавшем его бою');
		}

		if ($user->injury?->isFuture() && $user->injury_type > 2) {
			throw new Exception('С тяжелой травмой в бой нельзя!');
		}

		if ($type == 2 && $user->level > $enemy->level) {
			throw new Exception('Выбери равного или более сильного противника!');
		}

		if ($type == 2 && $enemy->location !== $user->location) {
			throw new Exception('Для нападния Вам необходимо находится в одной комнате!');
		}

		if ($user->hp_now <= 0 || $user->hp_now < ($user->hp_max * 0.33)) {
			throw new Exception('Вы слишком ослаблены для боя!');
		}

		if ($type == 2 && ($enemy->online->diffInSeconds() < 30) && !$enemy->battle_id && $enemy->rank == 60) {
			throw new Exception('Бот <u>' . $enemy->name . '</u> еще не восстановил свой уровень жизни!');
		}

		if ($enemy->rank == 60 && $enemy->battle_id) {
			$skip = false;

			$players = User::query()
				->where('battle_id', $enemy->battle_id)
				->whereKeyNot($enemy)
				->get();

			foreach ($players as $player) {
				if ($player->online->diffInMinutes() > 10) {
					BattleMember::query()
						->where('battle_id', $enemy->battle_id)
						->whereBelongsTo($player)
						->delete();

					$player->losses -= 1;
					$player->battle()->associate(null);
					$player->save();
				} else {
					$skip = true;
				}
			}

			if (!$skip) {
				$enemy->battle()->associate(null);
			}
		}

		if ($enemy->rank == 60 && !$enemy->battle_id) {
			$enemy->calculate();
			$enemy->hp_now = $enemy->hp_max;
			$enemy->online = now();
			$enemy->save();
		}

		if ($enemy->battle) {
			$battle = $enemy->battle;

			$prt = $battle->members()
				->whereBelongsTo($enemy)
				->first();

			$side = ($prt->side == 0 ? 1 : 0);

			$member = $battle->members()->create([
				'user_id' => $user->id,
				'side' => $side,
				'exp' => self::getBaseLevelExp($user->level),
			]);

			$battle->logs()
				->make([
					'comment_id' => 74,
				])
				->member()->associate($member)
				->save();

			$battle->type = BattleType::GROUP;
			$battle->status = BattleStatus::ACTIVE;
			$battle->save();

			$user->battle()->associate($battle);
			$user->save();
		} else {
			$battle = new Battle();
			$battle->started_at = now();
			$battle->round_at = now();
			$battle->type = BattleType::DUEL;
			$battle->timeout = 180;
			$battle->status = BattleStatus::ACTIVE;
			$battle->save();

			$memberUser = $battle->members()->create([
				'user_id' => $user->id,
				'side' => 0,
				'exp' => self::getBaseLevelExp($user->level),
			]);

			$battle->members()->create([
				'user_id' => $enemy->id,
				'side' => 1,
				'exp' => self::getBaseLevelExp($enemy->level),
			]);

			$user->battle()->associate($battle);
			$user->save();

			$enemy->battle()->associate($battle);
			$enemy->save();

			$battle->logs()
				->make([
					'comment_id' => 71,
				])
				->member()->associate($memberUser)
				->save();
		}
	}

	public static function attackWithMagic(User $user, User $enemy, bool $blood): void
	{
		if ($user->is($enemy)) {
			throw new Exception('Нельзя напасть на себя');
		}

		if ($user->battle_id) {
			throw new Exception('В бою нападение невозможно');
		}

		if ($enemy->isBot() || $enemy->rank > 13) {
			throw new Exception('На этого персонажа нельзя напасть');
		}

		if ($enemy->attack_protection?->isFuture()) {
			throw new Exception('Персонаж защищён от нападения');
		}

		if ($enemy->injury?->isFuture()) {
			throw new Exception('Нельзя напасть на травмированного персонажа');
		}

		if ($enemy->currentLocation()->is('training-arena') || $enemy->prison?->isFuture() || !$enemy->isFree()) {
			throw new Exception('Персонаж сейчас недоступен для нападения');
		}

		if ($enemy->hp_now <= 5) {
			throw new Exception('Персонаж слишком слаб для поединка');
		}

		if ($user->level - $enemy->level >= 2) {
			throw new Exception('Нельзя нападать на персонажа ниже вас на два уровня или больше');
		}

		if (self::getCurrentUserRequest($user) || self::getCurrentUserRequest($enemy)) {
			throw new Exception('Один из персонажей уже участвует в заявке на бой');
		}

		$joining = $enemy->battle_id !== null;

		if ($joining) {
			$battle = $enemy->battle;

			if (!$battle || $battle->status !== BattleStatus::ACTIVE || $battle->result !== null) {
				throw new Exception('Этот бой уже завершён или ещё не начался');
			}

			if ($battle->type === ($blood ? BattleType::CHAOS : BattleType::ALIGN)) {
				throw new Exception('Этим свитком нельзя вмешаться в такой бой');
			}

			$member = $battle->members()->whereBelongsTo($enemy)->first();

			if (!$member || $member->died_at) {
				throw new Exception('Персонаж уже выбыл из боя');
			}
		}

		self::fightLocked($user, $enemy);

		$battle = $user->battle;

		if ($blood) {
			$battle->is_blood = true;
		}

		if (!$joining) {
			$battle->timeout = 60;
		}

		$battle->save();
	}

	public static function changeSideWithMagic(User $user, User $target): void
	{
		if ($user->is($target)) {
			throw new Exception('Нельзя переманить себя');
		}

		if (!$user->battle_id || $user->battle_id !== $target->battle_id) {
			throw new Exception('Для переманивания нужно находиться в одном бою');
		}

		$battle = $target->battle;

		if (!$battle || $battle->status !== BattleStatus::ACTIVE || $battle->result !== null || $battle->type === BattleType::DUEL) {
			throw new Exception('Переманивание доступно только в продолжающемся групповом бою');
		}

		$members = $battle->members()->get()->keyBy('user_id');
		$fighter = $members->get($user->id);
		$enemy = $members->get($target->id);

		if (!$fighter || !$enemy || $fighter->died_at || $enemy->died_at || $user->hp_now <= 0 || $target->hp_now <= 0) {
			throw new Exception('Переманивать могут только живые участники боя');
		}

		if ($fighter->side === $enemy->side) {
			throw new Exception('Персонаж уже сражается на вашей стороне');
		}

		$enemy->side = $fighter->side;
		$enemy->save();

		// После смены команды разрешаем заново выбрать ходы, направленные на новых союзников.
		$turns = $battle->logs()
			->with(['member', 'enemy'])
			->where('round', $battle->round)
			->whereNotNull('enemy_id')
			->get();

		foreach ($turns as $turn) {
			if ($turn->member->side === $turn->enemy->side) {
				$turn->member->finished_at = null;
				$turn->member->save();
				$turn->delete();
			}
		}
	}

	public static function fightMirror(User $user): void
	{
		if ($user->battle_id || self::getCurrentUserRequest($user)) {
			throw new Exception('Нельзя вызвать клона во время боя или ожидания заявки');
		}

		if ($user->level > 5) {
			throw new Exception('Вызов клона доступен только до 5-го уровня включительно');
		}

		if ($user->hp_now <= 0 || $user->hp_now < $user->hp_max * 0.33) {
			throw new Exception('Вы слишком ослаблены для боя');
		}

		$clone = User::create([
			'email' => Str::uuid()->toString() . '@mirror.invalid',
			'name' => 'Клон ' . mb_substr($user->name, 0, 95),
			'rank' => 60,
			'is_clone' => true,
			'level' => $user->level,
			'up' => $user->up,
			'exp' => $user->exp,
			'gender' => $user->gender,
			'image' => $user->image,
			'location' => $user->location,
			'strength' => $user->strength,
			'dexterity' => $user->dexterity,
			'agility' => $user->agility,
			'vitality' => $user->vitality,
			'magic' => $user->magic,
			'intelligence' => $user->intelligence,
			'magic_resistance' => $user->magic_resistance,
			'hp_now' => $user->hp_max,
			'energy_now' => $user->energy_max,
			'online' => now(),
		]);

		$cloneSlots = $clone->getSlot();

		foreach ($user->getSlot()->getItems() as $item) {
			$copy = $item->replicate();
			$copy->user()->associate($clone);
			$copy->save();

			for ($slot = 1; $slot <= config('game.max_slots'); $slot++) {
				if ($user->getSlot()->{'i' . $slot} === $item->id) {
					$cloneSlots->{'i' . $slot} = $copy->id;
				}
			}
		}

		$cloneSlots->save();

		foreach ($user->effects()->whereFuture('date')->get() as $effect) {
			$copy = $effect->replicate();
			$copy->user()->associate($clone);
			$copy->save();
		}

		self::fightLocked($user, $clone->fresh());
	}

	public static function getCurrentUserRequest(User $user): ?BattleMember
	{
		return BattleMember::query()
			->with('battle')
			->whereBelongsTo($user)
			->whereHas('battle', function (Builder $query) {
				$query->where('status', BattleStatus::WAITING)
					->where(function (Builder $query) {
						$query->where('type', '!=', BattleType::DUEL)
							->orWhere('started_at', '>', now());
					});
			})
			->first();
	}

	public static function offerValidation(User $user, BattleType $battleType): void
	{
		if (!in_array($battleType, [BattleType::DUEL, BattleType::GROUP, BattleType::CHAOS], true)) {
			throw new Exception('Неизвестный тип боя');
		}

		if ($user->battle_id) {
			throw new Exception('Вы уже участвуете в бою!');
		}

		if ($user->injury?->isFuture()) {
			throw new Exception('Вы не можете драться, пока не зажила травма. Вам необходим отдых!');
		}

		if (!$user->currentLocation()->is('arena')) {
			throw new Exception('Для участия в поединках необходимо переместиться на арену.');
		}

		if ($battleType === BattleType::GROUP && $user->level < 2) {
			throw new Exception('Извините, групповые бои со 2-го уровня');
		}

		if ($battleType === BattleType::CHAOS && $user->level < 3) {
			throw new Exception('Извините, хаотические бои с 3-го уровня');
		}
	}

	public static function createOffer(User $user, BattleType $battleType, array $options): Battle
	{
		return DB::transaction(function () use ($user, $battleType, $options) {
			$user->refreshForUpdate();

			self::checkOfferParticipant($user, $battleType);

			$levelRange = match ($options['offer_level'] ?? 1) {
				2 => [$user->level, $user->level],
				3 => [0, $user->level],
				4 => [0, $user->level - 1],
				default => [0, 12],
			};

			$capacity = match ($battleType) {
				BattleType::DUEL => 1,
				BattleType::GROUP => $options['capacity'] ?? 2,
				default => 50,
			};

			$timeout = match ($options['timeout'] ?? 3) {
				1 => 90,
				5 => 300,
				10 => 600,
				default => 180,
			};

			$startedAt = now()->addSeconds(
				$battleType === BattleType::DUEL ? 600 : ($options['time_battle_start'] ?? 180)
			);

			$battle = Battle::query()->create([
				'status' => BattleStatus::WAITING,
				'type' => $battleType,
				'timeout' => $timeout,
				'comment' => $options['comment'] ?? '',
				'started_at' => $startedAt,
				'capacity' => $capacity,
				'min_level' => $battleType === BattleType::DUEL ? null : $levelRange[0],
				'max_level' => $battleType === BattleType::DUEL ? null : $levelRange[1],
				'is_blood' => $battleType !== BattleType::GROUP && ($options['blood'] ?? false),
				'use_weapons' => $battleType !== BattleType::DUEL || !($options['unarmed'] ?? false),
			]);

			$battle->members()->create([
				'user_id' => $user->id,
				'side' => 0,
				'exp' => self::getBaseLevelExp($user->level),
			]);

			return $battle;
		});
	}

	public static function takeOffer(Battle $battle, User $user, int $side = 0): void
	{
		DB::transaction(function () use ($battle, $user, $side) {
			$battle = Battle::query()
				->lockForUpdate()
				->find($battle->id);

			if (!$battle || $battle->status !== BattleStatus::WAITING || !$battle->started_at?->isFuture()) {
				throw new Exception('Заявки не существует или истёк срок её размещения');
			}

			$user->refreshForUpdate();

			self::checkOfferParticipant($user, $battle->type);

			$members = $battle->members()
				->with('user')
				->get();

			if ($battle->type === BattleType::DUEL) {
				if ($members->count() !== 1) {
					throw new Exception('Кто-то оказался быстрее и перехватил заявку');
				}

				$opponent = $members->firstOrFail()->user;

				if ($opponent->ip === $user->ip && !$user->isAdmin()) {
					throw new Exception('Вы не можете выступать против персонажа с таким же IP как у вас!');
				}

				$side = 1;
			} else {
				if ($user->level < $battle->min_level || $user->level > $battle->max_level) {
					throw new Exception('Ваш уровень не соответствует условиям заявки');
				}

				if ($battle->type === BattleType::CHAOS) {
					$side = 0;
					$count = $members->count();
				} else {
					if (!in_array($side, [0, 1], true)) {
						throw new Exception('Неизвестная команда');
					}

					$count = $members->where('side', $side)->count();
				}

				if ($count >= $battle->capacity) {
					throw new Exception('Группа уже набрана!');
				}
			}

			$battle->members()->create([
				'user_id' => $user->id,
				'side' => $side,
				'exp' => self::getBaseLevelExp($user->level),
			]);

			if (isset($opponent)) {
				ChatService::sendSystemMessage($user->name . ' принял Вашу заявку!', [$opponent]);
			}
		});
	}

	private static function checkOfferParticipant(User $user, BattleType $battleType): void
	{
		self::offerValidation($user, $battleType);

		if (self::getCurrentUserRequest($user)) {
			throw new Exception('Для начала с одной заявкой разберись...');
		}

		$user->calculate();

		if ($user->hp_now < $user->hp_max / 3) {
			throw new Exception('Вы слишком ослаблены для поединка! Восстановитесь...');
		}
	}

	public static function withdrawOffer(User $user, bool $dismissOpponent = false): void
	{
		DB::transaction(function () use ($user, $dismissOpponent) {
			$offer = self::getCurrentUserRequest($user);

			$battle = null;

			if ($offer) {
				$battle = Battle::query()
					->lockForUpdate()
					->find($offer->battle_id);
			}

			if (!$battle || $battle->status !== BattleStatus::WAITING || $battle->type !== BattleType::DUEL || !$battle->started_at?->isFuture()) {
				throw new Exception('Заявки не существует или истёк срок её размещения');
			}

			$member = $battle->members()
				->whereBelongsTo($user)
				->first();

			if (!$member || ($dismissOpponent && $member->side != 0)) {
				throw new Exception('Вы не можете отказаться за другого участника');
			}

			if ($member->side == 1) {
				$member->delete();

				return;
			}

			$opponent = $battle->members()
				->with('user')
				->where('side', 1)
				->first();

			if ($dismissOpponent) {
				$opponent?->delete();
			} else {
				$battle->members()->delete();
				$battle->delete();
			}

			if ($opponent) {
				ChatService::sendSystemMessage($user->name . ' отказал в поединке!', [$opponent->user]);
			}
		});
	}

	public static function startOffer(User $user, BattleType $battleType): bool
	{
		if (!in_array($battleType, [BattleType::DUEL, BattleType::GROUP, BattleType::CHAOS], true)) {
			return false;
		}

		return DB::transaction(function () use ($user, $battleType) {
			$now = now();

			$query = Battle::query()
				->where('type', $battleType)
				->where('status', BattleStatus::WAITING)
				->whereHas('members', fn(Builder $query) => $query->whereBelongsTo($user));

			if ($battleType === BattleType::DUEL) {
				$query->where('started_at', '>', $now);
			} else {
				$query->where('started_at', '<=', $now->subSeconds(10));
			}

			$battle = $query->lockForUpdate()
				->first();

			if (!$battle) {
				return false;
			}

			$members = $battle->members()
				->with('user')
				->lockForUpdate()
				->get();

			$sideCount = $members->pluck('side')
				->unique()->count();

			if ($battleType === BattleType::DUEL && !$members->contains(fn(BattleMember $member) => $member->user_id === $user->id && $member->side == 0)) {
				throw new Exception('Начать поединок может только автор заявки');
			}

			if ($battleType === BattleType::DUEL && $sideCount !== 2) {
				return false;
			}

			if (($battleType === BattleType::GROUP && $sideCount < 2) || ($battleType === BattleType::CHAOS && $members->pluck('user_id')->unique()->count() < 4)) {
				User::query()->whereBelongsTo($battle)
					->update(['battle_id' => null]);

				$battle->members()->delete();
				$battle->delete();

				ChatService::sendSystemMessage('Ваш бой не может начаться, т.к. группа не набрана!', [$user]);

				return false;
			}

			if ($battleType === BattleType::CHAOS) {
				$members = $members->shuffle();
				$pairedCount = $members->count() - $members->count() % 2;

				foreach ($members as $index => $member) {
					$member->side = $index >= $pairedCount || in_array($index % 4, [1, 2], true) ? 1 : 0;
					$member->save();
				}
			}

			$battle->status = BattleStatus::ACTIVE;
			$battle->round_at = $now;
			$battle->save();

			$battle->logs()->create([
				'member_id' => $members->firstOrFail()->id,
				'date' => $now,
				'comment_id' => 71,
			]);

			foreach ($members as $member) {
				$member->user->battle()->associate($battle);
				$member->user->save();

				if ($battleType === BattleType::DUEL && !$battle->use_weapons) {
					InventoryService::unsetAllObject($member->user);
				}

				ChatService::sendSystemMessage(
					'Часы показывали ' . $now->format('d.m.y H:i') . ', когда Ваш бой начался!',
					[$member->user],
				);
			}

			return true;
		});
	}

	public static function setInjury(
		User $user,
		User $enemy,
		int $level,
		CarbonImmutable $time,
		Randomizer $randomizer,
	): bool {
		if ($enemy->rank == 60) {
			return false;
		}

		if ($enemy->injury?->greaterThan($time)) {
			return false;
		}

		$duration = 300 + (300 * $level);

		$injuries = self::INJURIES[$level];
		$param = $injuries[$randomizer->getInt(0, count($injuries) - 1)];

		$strength = $dexterity = $agility = 0;
		$combatStats = $enemy->getCombatStats();

		if ($param['param'] == 'strength') {
			$strength = round($combatStats->strength * ($level / 3.2)) * (-1);
		} elseif ($param['param'] == 'dexterity') {
			$dexterity = round($combatStats->dexterity * ($level / 3.2)) * (-1);
		} elseif ($param['param'] == 'agility') {
			$agility = round($combatStats->agility * ($level / 3.2)) * (-1);
		}

		$enemy->injury = $time->addSeconds($duration);
		$enemy->injury_type = $level;
		$enemy->save();

		$enemy->effects()->create([
			'type' => 3,
			'date' => $enemy->injury,
			'strength' => $strength,
			'dexterity' => $dexterity,
			'agility' => $agility,
		]);

		$message = $enemy->name . ' получает в бою ';

		if ($level == 1) {
			$message .= 'лёгкую травму';
		} elseif ($level == 2) {
			$message .= 'среднюю травму';
		} elseif ($level == 3) {
			$message .= 'тяжёлую травму';
		} else {
			$message .= 'неизлечимую травму';
		}

		$message .= ' ' . $param['name'] . ' от ' . $user->name
			. ', которая очень сильно повлияла на параметр ' . __('stats.' . $param['param']);

		ChatService::sendSystemMessage($message, [$enemy]);

		return true;
	}

	/** @return list<UserItem> */
	public static function wearout(User $user, Randomizer $randomizer): array
	{
		return DB::transaction(function () use ($user, $randomizer) {
			$slots = $user->slots()
				->lockForUpdate()
				->firstOrFail();

			$user->setRelation('slots', $slots);

			$items = $user->items()
				->whereIn('id', $slots->getItemsId())
				->whereNot('type', 12)
				->orderBy('id')
				->lockForUpdate()
				->get();

			if ($items->isEmpty()) {
				return [];
			}

			$itemKeys = $randomizer->pickArrayKeys($items->all(), $randomizer->getInt(1, $items->count()));
			$wornItems = $items->toBase()->only($itemKeys);

			foreach ($wornItems as $item) {
				$item->wearout += 1;
				$item->save();

				if ($item->wearout_max <= $item->wearout) {
					InventoryService::unsetObject($user, $item->onset);
				}
			}

			DB::afterCommit(fn() => $slots->clearCache());

			return $wornItems->values()->all();
		});
	}

	public static function getBaseLevelExp(int $level): int
	{
		$baseExperience = Cache::remember('battle:base_level_exp', 86400, function () {
			return Level::query()
				->where('up', 0)
				->pluck('base', 'level')
				->all();
		});

		return $baseExperience[$level] ?? 0;
	}
}
