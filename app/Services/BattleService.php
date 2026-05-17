<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BattleService
{
	public static function fight(User $user, User $enemy, $type = 1)
	{
		if ($enemy->is($user)) {
			throw new Exception('Нападение на самого себя - это уже мазохизм...');
		}

		if ($type == 2 && $enemy->rank != 60) {
			throw new Exception('Персонаж <u>' . $enemy->name . '</u> не является ботом!');
		}

		if ($user->injury > time() && $user->injury_type > 2) {
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

			$battle->type = 2;
			$battle->status = 'active';
			$battle->save();

			$user->battle()->associate($battle);
			$user->save();
		} else {
			$battle = new Battle();
			$battle->started_at = now();
			$battle->round_at = now();
			$battle->type = 1;
			$battle->timeout = 180;
			$battle->status = 'active';
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
			->whereBelongsTo($user)
			->whereHas('battle', function (Builder $query) {
				$query->where('started_at', '>', now())
					->where('Status', 'waiting');
			})
			->first();
	}

	public static function takeOffer(Battle $battle, User $user)
	{
		$existOffer = self::getCurrentUserRequest($user);

		$message = '';

		if (isset($existOffer)) {
			throw new Exception('Для начала с одной заявкой разберись...');
		}

		if ($user->hp_now < $user->hp_max / 3) {
			throw new Exception('Вы слишком ослаблены для поединка, подлечитесь!');
		}

		if ($battle->type == 1) {
			$battle->loadMissing(['members', 'members.user']);

			switch ($battle->members->count()) {
				case 1:
					$opponent = $battle->members
						->where('side', 0)
						->first();

					if (!$opponent) {
						throw new Exception('Оппонент не найден');
					}

					if ($opponent->user->ip == $user->ip && !$user->isAdmin()) {
						throw new Exception('Вы не можете выступать против персонажа с таким же IP как у вас!');
					}

					$battle->members()->create([
						'user_id' => $user->id,
						'side' => 1,
						'exp' => self::getBaseLevelExp($user->level),
					]);

					ChatService::insertInChat($opponent->user, '<b>' . $user->name . '</b> принял Вашу заявку!');

					break;
				case 2:
					throw new Exception('Кто-то оказался быстрее и перехватил заявку');
				default:
					throw new Exception('Боец отозвал заявку или её не существует!');
			}
		} elseif ($battle->type == 2) {
			if ($user->level < 2) {
				throw new Exception('Извините, групповые бои с 2-ого уровня');
			}

			$side = min(1, max(0, request()->integer('battle_side')));

			$side_0 = $battle->members->where('side', 0)->count();
			$side_1 = $battle->members->where('side', 1)->count();

			if ($side_0 >= $battle->capacity && $side == 0) {
				throw new Exception('Группа уже набрана!');
			}

			if ($side_1 >= $battle->capacity && $side == 1) {
				throw new Exception('Группа уже набрана!');
			}

			if ($user->level < $battle->min_level && $side == 0) {
				throw new Exception('Эта заявка не может быть принята Вами!');
			}

			if (($battle->min_level == $battle->max_level) && ($user->level != $battle->min_level) && $side == 0) {
				throw new Exception('Эта заявка не может быть принята Вами!');
			}

			if ($user->level > $battle->max_level && $side == 0) {
				throw new Exception('Эта заявка не может быть принята Вами!');
			}

			if ($user->level < $battle->min_level && $side == 1) {
				throw new Exception('Эта заявка не может быть принята Вами!');
			}

			if (($battle->min_level == $battle->max_level) && ($user->level != $battle->min_level) && $side == 1) {
				throw new Exception('Эта заявка не может быть принята Вами!');
			}

			if ($user->level > $battle->max_level && $side == 1) {
				throw new Exception('Эта заявка не может быть принята Вами!');
			}

			$battle->members()->create([
				'user_id' => $user->id,
				'side' => $side,
				'exp' => self::getBaseLevelExp($user->level),
			]);
		} elseif ($battle->type == 3) {
			if ($user->level < 3) {
				throw new Exception('Извините, хаотические бои с 3-ого уровня');
			} else {
				if ($user->level < $battle->min_level) {
					throw new Exception('Эта заявка не может быть принята Вами!');
				}

				if (($battle->min_level == $battle->max_level) && ($user->level != $battle->min_level)) {
					throw new Exception('Эта заявка не может быть принята Вами!');
				}

				if ($user->level > $battle->max_level) {
					throw new Exception('Эта заявка не может быть принята Вами!');
				}

				$battle->members()->create([
					'user_id' => $user->id,
					'side' => 0,
					'exp' => self::getBaseLevelExp($user->level),
				]);
			}
		}

		$user->battle()->associate($battle);
		$user->save();

		return $message;
	}

	public static function getBaseLevelExp(int $lvl): int
	{
		$level = Level::query()->where('level', $lvl)->first();

		return $level->base ?? 0;
	}
}
