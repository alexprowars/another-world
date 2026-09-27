<?php

namespace App\Services;

use App\Engine\Battle\BattleStatus;
use App\Engine\Battle\BattleType;
use App\Exceptions\Exception;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BattleService
{
	public static function fight(User $user, User $enemy, int $type = 1): void
	{
		if ($enemy->is($user)) {
			throw new Exception('Нападение на самого себя - это уже мазохизм...');
		}

		if ($type == 2 && $enemy->rank != 60) {
			throw new Exception('Персонаж <u>' . $enemy->name . '</u> не является ботом!');
		}

		if ($user->injury?->isFuture() && $user->injury_type > 2) {
			throw new Exception('С тяжелой травмой в бой нельзя!');
		}

		if ($type == 2 && $user->level > $enemy->level) {
			throw new Exception('Выбери равного или более сильного противника!');
		}

		if ($type == 2 && $enemy->room != 2) {
			throw new Exception('Для нападния Вам необходимо находится в одной комнате!');
		}

		if ($user->hp_now < ($user->hp_max * 0.33)) {
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

		if (!in_array($user->room, [1, 2, 3, 4], true)) {
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
				ChatService::insertInChat($opponent, '<b>' . e($user->name) . '</b> принял Вашу заявку!');
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
				ChatService::insertInChat($opponent->user, '<b>' . e($user->name) . '</b> отказал в поединке!');
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

				ChatService::insertInChat($user, 'Ваш бой не может начаться, т.к. группа не набрана!');

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

				ChatService::insertInChat($member->user, 'Часы показывали <U>' . $now->format('d.m.y H:i') . '</U>, когда Ваш бой начался!');
			}

			return true;
		});
	}

	public static function getBaseLevelExp(int $lvl): int
	{
		$level = Level::query()->where('level', $lvl)->first();

		return $level->base ?? 0;
	}
}
