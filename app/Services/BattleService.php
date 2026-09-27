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
			->whereBelongsTo($user)
			->whereHas('battle', function (Builder $query) {
				$query->where('started_at', '>', now())
					->where('Status', 'waiting');
			})
			->first();
	}

	public static function createOffer(User $user, BattleType $battleType)
	{
		$userOffer = self::getCurrentUserRequest($user);

		switch (request()->integer('timeout')) {
			case 1:
				$timeout = 90;
				break;
			case 3:
				$timeout = 180;
				break;
			case 5:
				$timeout = 300;
				break;
			case 10:
				$timeout = 600;
				break;
			default:
				$timeout = 180;
				break;
		}

		$comment = htmlspecialchars(request()->post('comment', ''));

		$message = '';

		if ($userOffer) {
			throw new Exception('Для начала с одной заявкой разберись...');
		}

		if ($user->hp_now < $user->hp_max / 3) {
			throw new Exception('Вы слишком ослаблены для поединка! Восстановитесь...');
		}

		$battle = new Battle();
		$battle->status = BattleStatus::WAITING;
		$battle->timeout = $timeout;
		$battle->comment = $comment;

		switch ($battleType) {
			case BattleType::DUEL:
				$battle->started_at = now()->addSeconds(600);
				$battle->type = BattleType::DUEL;
				$battle->is_blood = request()->boolean('blood');
				$battle->use_weapons = !request()->boolean('kulak');

				break;

			case 2:
				if ($user->level < 2) {
					throw new Exception('Извините, групповые бои с 2-ого уровня');
				}

				$time_battle_start = request()->integer('time_battle_start');

				if ($time_battle_start != 180 && $time_battle_start != 300 && $time_battle_start != 600 && $time_battle_start != 900) {
					$time_battle_start = 180;
				}

				switch (request()->integer('offer_level')) {
					case 2:
						$level_min = $user->level;
						$level_max = $user->level;
						break;
					case 3:
						$level_min = 0;
						$level_max = $user->level;
						break;
					case 4:
						$level_min = 0;
						$level_max = $user->level - 1;
						break;
					default:
						$level_min = 0;
						$level_max = 12;
				}

				$capacity 	= request()->integer('capacity', 2);

				// Размеры команд
				if ($capacity < 2 || $capacity > 25) {
					$capacity = 2;
				}

				$battle->started_at = now()->addSeconds($time_battle_start);
				$battle->type = BattleType::GROUP;
				$battle->capacity = $capacity;
				$battle->min_level = $level_min;
				$battle->max_level = $level_max;

				break;

			case 3:
				if ($user->level < 3) {
					throw new Exception('Извините, хаотические бои с 3-ого уровня');
				}

				$time_battle_start = request()->integer('time_battle_start');

				$alg = request()->integer('alg', 1);
				$alg = min(1, max(0, $alg));

				$inv = request()->integer('inv', 1);
				$inv = min(1, max(0, $inv));

				// Время до начала поединка
				if ($time_battle_start != 180 && $time_battle_start != 300 && $time_battle_start != 600 && $time_battle_start != 900) {
					$time_battle_start = 180;
				}

				// Уровни
				switch (request()->integer('offer_level')) {
					case 2:
						$level_min = $user->level;
						$level_max = $user->level;
						break;
					case 3:
						$level_min = 0;
						$level_max = $user->level;
						break;
					case 4:
						$level_min = 0;
						$level_max = $user->level - 1;
						break;
					default:
						$level_min = 0;
						$level_max = 12;
				}

				$battle->started_at = now()->addSeconds($time_battle_start);
				$battle->type = BattleType::CHAOS;
				$battle->capacity = 50;
				$battle->min_level = $level_min;
				$battle->max_level = $level_max;
				$battle->is_blood = request()->boolean('blood');

				//						'alg'				=> $alg,
				//						'inv'				=> $inv,

				break;
			default:
				throw new Exception('unknown battle type');
		}

		$battle->save();
		$battle->members()->create([
			'user_id' => $user->id,
			'side' => 0,
			'exp' => self::getBaseLevelExp($user->level),
		]);

		return $message;
	}

	public static function takeOffer(Battle $battle, User $user)
	{
		$existOffer = self::getCurrentUserRequest($user);

		if (isset($existOffer)) {
			throw new Exception('Для начала с одной заявкой разберись...');
		}

		if ($user->hp_now < $user->hp_max / 3) {
			throw new Exception('Вы слишком ослаблены для поединка, подлечитесь!');
		}

		if ($battle->type == BattleType::DUEL) {
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
		} elseif ($battle->type == BattleType::GROUP) {
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
		} elseif ($battle->type == BattleType::CHAOS) {
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
	}

	public static function getBaseLevelExp(int $lvl): int
	{
		$level = Level::query()->where('level', $lvl)->first();

		return $level->base ?? 0;
	}
}
