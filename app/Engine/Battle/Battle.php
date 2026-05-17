<?php

namespace App\Engine\Battle;

use App\Exceptions\Exception;
use App\Models\BattleLog;
use App\Models\BattleMember;
use App\Models\Level;
use App\Models\User;
use App\Models\UserItem;
use App\Models\Battle as BattleModel;
use App\Services\ChatService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

define('PRECESSION', '100000');
// STATS_VS_MOD - параметр, задающий соотношение между статами и модификаторами. 1 стат = r модификаторов.
// STATS_VS_HP - параметр, задающий соотношение между статами и хитпоинтами. 1 стат = hp хитпоинтов.
// DAM_AVE - параметр, задающий соотношение между статами и средним уроном. 1 стат = dam_ave урона.
// ARMOR_AVE - параметр, задающий соотношение между статами и броней. 1 стат = armor_ave урона.
define('STATS_VS_MOD', 5);
define('STATS_VS_HP', '6');
define('DAM_AVE', '1.33');
define('ARMOR_AVE', '30');
// TRAVMA_LIGHT - коэффициент для определения лёгкой травмы
// TRAVMA_MEDIUM - коэффициент для определения средней травмы
// TRAVMA_HARD - коэффициент для определения тяжёлой травмы
define('TRAVMA_LIGHT', 1.75);
define('TRAVMA_MEDIUM', 2.5);
define('TRAVMA_HARD', 3);

class Battle
{
	protected $numKicks = 1;
	protected $numBlocks = 1;
	protected BattleMember $fighter;

	public function __construct(protected BattleModel $battle, protected User $user)
	{
		$battle->loadMissing([
			'members', 'members.user',
		]);

		$this->fighter = $battle->members->where('user_id', $this->user->id)->first();
	}

	protected $injury = [
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

	public function init()
	{
	}

	public function show()
	{
		$json = [
			'time' => now()->toAtomString(),
			'action' => 'impactForm',
			'result' => null,
		];

		// Основные боевые константы
		/** @var array $priem_full */
		include(resource_path('/data/battle.php'));

		$this->user->calculate();
		$this->calculateKickAndBlockCount();

		$logId = request()->integer('lastLogId');

		if (request()->has('ability') && $abilityId = request()->integer('ability')) {
			$ability = $priem_full[$abilityId] ?? null;

			if ($ability) {
				$this->fighter->ability = $abilityId;
				$this->fighter->wait = $ability['wait'] ?? 0;
				$this->fighter->time = $ability['time'] ?? 0;
				$this->fighter->hits -= $ability['hit'] ?? 0;
				$this->fighter->blocks -= $ability['block'] ?? 0;
				$this->fighter->crits -= $ability['crit'] ?? 0;
				$this->fighter->spirit -= $ability['magic'] ?? 0;
				$this->fighter->parry -= $ability['parry'] ?? 0;
				$this->fighter->hp -= $ability['damage'] ?? 0;
				$this->fighter->save();
			}
		}

		$this->processKick();
		$this->checkFinished();

		// Вычисляем время таймаута
		$timeout = $this->battle->timeout - $this->battle->round_at->diffInSeconds();

		$victims = [];
		$random = 0;

		// ----- # HP равно нулю, проигрываем, выигрываем, или ждём окончания боя # ----- //
		if ($this->user->hp_now <= 0 || $this->fighter->died_at || $this->battle->result) {
			if ($this->user->hp_now <= 0 && !$this->fighter->died_at) {
				$this->fighter->died_at = now();
				$this->fighter->save();
			}

			$this->checkBattleResult();
		} else {
			$json['opponents'] = [];

			$accept = 0;

			$n = 0;

			$opponents = $this->battle->members
				->where('side', $this->fighter->side == 1 ? 0 : 1)
				->whereNull('died_at')
				->filter(function (BattleMember $member) {
					return $member->user->hp_now > 0;
				});

			// Если в бою есть противники
			if ($opponents->isNotEmpty()) {
				foreach ($opponents as $opponent) {
					$victims[$n] = $opponent->id;

					if (request()->has('opponent') && request()->integer('opponent') == $opponent->id) {
						$accept = 1;
					}

					$json['opponents'][] = [
						'id' => $opponent->id,
						'name' => $opponent->user->name,
						'level' => $opponent->user->level,
					];

					$n++;
				}

				// Если ты закончил раунд
				if ($this->fighter->finished_at) {
					// ----------------------------- # Выиграли по таймауту # -------------------------- //
					if ($timeout <= 0) {
						$this->timeout();

						$json['action'] = 'refresh';
					} else {
						$json['action'] = 'waitImpact';
					}
				} else {
					$random = 0; // rand(0, $n - 1);

					if ($accept == 1) {
						$victims[0] = request()->integer('opponent');
					}

					// ----------------------------- # Проигрыш по таймауту # -------------------------- //
					if ($timeout <= 0) {
						$this->timeout();

						$json['action'] = 'refresh';
					}
					// --------------------------------- # Конец # ------------------------------------- //

					if ($timeout > 0 && (isset($victims[$random]) || !$this->fighter->finished_at)) {
						// Если никого не можеш ударить то удар и блок поставить не можеш
						if (!isset($victims[$random])) {
							$this->numBlocks = 0;
							$this->numKicks = 0;
						}
					}
				}
			} else {
				$this->battleResult(3);
			}
		}

		if ($this->battle->result) {
			$json['action'] = 'finishBattle';

			if ($this->battle->result == 1) {
				$json['result'] = 'draw';
			} elseif ($this->battle->result == 2) {
				if ($this->fighter->side == 0) {
					$json['result'] = 'lose';
				} else {
					$json['result'] = 'win';
				}
			} elseif ($this->battle->result == 3) {
				if ($this->fighter->side == 0) {
					$json['result'] = 'win';
				} else {
					$json['result'] = 'lose';
				}
			}
		}

		if (empty($json['action'])) {
			if ($this->battle->type == BattleType::DUEL && $this->user->room == BattleType::GROUP) {
				$json['action'] = 'impactForm';
			} else {
				$json['action'] = 'mapForm';
			}
		}

		$json['kicks'] = $this->numKicks;
		$json['blocks'] = $this->numBlocks;
		$json['abilities'] = [
			'list' => [],
		];

		$p_block = $this->fighter->blocks;
		$p_hit = $this->fighter->hits;
		$p_krit = $this->fighter->crits;
		$p_mag = $this->fighter->spirit;
		$p_parry = $this->fighter->parry;
		$p_hp = $this->fighter->hp;

		for ($i = 1; $i <= 10; $i++) {
			$json['abilities']['list']['p_' . $i] = null;
		}

		$abilities = $this->user->abilities()
			->pluck('ability', 'slot');

		foreach ($abilities as $slot => $ability) {
			if ($p_block < $priem_full[$ability]['block'] || $p_hit < $priem_full[$ability]['hit'] || $p_krit < $priem_full[$ability]['crit'] || $p_mag < $priem_full[$ability]['magic'] || $p_parry < $priem_full[$ability]['parry'] || $p_hp < $priem_full[$ability]['damage'] || $this->fighter->wait > 0) {
				$w = 1;
			} else {
				$w = 0;
			}

			$json['abilities']['list']['p_' . $slot] = [
				'id' => $ability,
				'n' => $priem_full[$ability]['name'],
				'b' => $priem_full[$ability]['block'],
				'h' => $priem_full[$ability]['hit'],
				'k' => $priem_full[$ability]['crit'],
				'm' => $priem_full[$ability]['magic'],
				'p' => $priem_full[$ability]['parry'],
				'd' => $priem_full[$ability]['damage'],
				'a' => $priem_full[$ability]['about'],
				'w' => $w,
			];
		}

		$json['abilities']['wait'] = $this->fighter->wait;
		$json['abilities']['time'] = $this->fighter->time;
		$json['abilities']['ability'] = $this->fighter->ability ? $priem_full[$this->fighter->ability]['name'] : null;
		$json['abilities']['points'] = [
			'blocks' => $p_block,
			'hits' => $p_hit,
			'crits' => $p_krit,
			'magic' => $p_mag,
			'parry' => $p_parry,
			'hp' => $p_hp,
		];

		$json['user'] = [
			'id' => $this->user->id,
			'rank' => $this->user->rank,
			'hp' => (int) floor($this->user->hp_now),
			'hp_max' => $this->user->hp_max,
			'energy' => (int) floor($this->user->energy_now),
			'energy_max' => $this->user->energy_max,
			'level' => $this->user->level,
			'tribe' => $this->user->tribe,
			'name' => $this->user->name,
			'avatar' => $this->user->getAvatar(),
			'items' => $this->user->getSlotsInfo(),
		];

		$json['teams'] = ['left' => [], 'right' => []];

		if (!$this->battle->result) {
			$command = ['left' => [], 'right' => []];

			// Построение комманд
			$fighters = $this->battle->members
				->whereNull('died_at')
				->filter(function (BattleMember $member) {
					return $member->user->hp_now > 0;
				})
				->sortBy([
					['user.rank', 'asc'],
					['user.level', 'asc'],
				]);

			foreach ($fighters as $fighter) {
				$command[$fighter->side == 0 ? 'left' : 'right'][] = [
					'id' => $fighter->user->id,
					'name' => $fighter->user->name,
					'hp' => (int) floor($fighter->user->hp_now),
					'level' => $fighter->user->level,
					'side' => $fighter->side,
					'finished' => $fighter->finished_at != null,
				];
			}

			if (!empty($command['left'])) {
				$json['teams']['left'] = $command['left'];
			}

			if (!empty($command['right'])) {
				$json['teams']['right'] = $command['right'];
			}
		}

		$json['oppenent_id'] = null;
		$json['opponent'] = null;

		if ($timeout && $this->user->hp_now > 0 && !$this->battle->result && isset($victims[$random])) {
			/** @var BattleMember $enemy */
			$enemy = $this->battle->members
				->where('id', $victims[$random])
				->first();

			$enemy->user->calculate();

			$json['opponent_id'] = $enemy->id;

			$json['opponent'] = [
				'id' => $enemy->user->id,
				'rank' => $enemy->user->rank,
				'hp' => (int) floor($enemy->user->hp_now),
				'hp_max' => $enemy->user->hp_max,
				'energy' => (int) floor($enemy->user->energy_now),
				'energy_max' => $enemy->user->energy_max,
				'level' => $enemy->user->level,
				'tribe' => $enemy->user->tribe_id,
				'name' => $enemy->user->name,
				'avatar' => $enemy->user->getAvatar(),
				'items' => $enemy->user->getSlotsInfo(),
			];
		}

		$json['damage'] = $this->fighter->damage;
		$json['id'] = $this->user->battle_id;
		$json['timeout_left'] = (int) max(0, $timeout);
		$json['timeout'] = $this->battle->timeout;

		$json['logs'] = [];

		$lastLogs = $this->battle->logs()
			->with(['member', 'member.user', 'enemy', 'enemy.user'])
			->orderByDesc('round')
			->orderByDesc('id')
			->where('id', '>', $logId)
			->get();

		foreach ($lastLogs as $turn) {
			$json['logs'][] = [
				'id' => $turn->id,
				'date' => $turn->date->toAtomString(),
				'round' => $turn->round,
				'user' => $turn->member->user->name ?? null,
				'side' => $turn->member->side,
				'hits' => $turn->hit,
				'damage' => $turn->damage,
				'blocks' => $turn->block,
				'enemy' => $turn->enemy->user->name ?? null,
				'enemy_blocks' => $turn->enemy_block,
				'comment' => $turn->comment_id,
				'my' => $this->user->is($turn->member->user) || $this->user->is($turn->enemy->user),
			];
		}

		return $json;
	}

	private function endRound()
	{
		$logs = $this->battle->logs()
			->with(['member', 'member.user'])
			->where('round', $this->battle->round)
			->orderBy('id')
			->get();

		foreach ($logs as $user) {
			foreach ($logs as $enemy) {
				if ($user->member->is($enemy->enemy)) {
					$user->member->user->calculate();
					$enemy->member->user->calculate();

					$damage = $this->kick($user, $enemy, $this->battle->round);

					//dump($user->member_id, $damage);

					if ($this->user->is($enemy->member->user)) {
						$this->user->hp_now -= $damage;
					}
				}
			}
		}

		//dd($this->battle->members->toArray());

		$this->battle->round++;
		$this->battle->round_at = now();
		$this->battle->save();

		$this->battle->members->each(function (BattleMember $member) {
			$member->finished_at = null;
			$member->save();
		});

		$this->battle->refresh();

		return true;
	}

	//	принцип действия: лёгкая травма лишает игрока 1/3 одного из статов
	//	средняя травма заберёт 2/3
	//	тяжёлая - в ноль.
	//	лёгкая травма даёт возможность играть относительно нормально
	//	средняя - только рукопашку и снимает все вещи
	//	тяжёлая - лишает возможности играть и снимает все вещи
	//	две лёгкие = 1 средняя
	//	все остальные комбинации = 1 тяжёлая
	private function setInjury(User $user, User $enemy, $level)
	{
		if ($enemy->rank == 60) {
			return false;
		}

		if ($enemy->injury?->isFuture()) {
			return false;
		}

		$time = 300 + (300 * $level);

		$param = $this->injury[$level][array_rand($this->injury[$level])];

		$strength = $dex = $agility = 0;

		if ($param['param'] == 'strength') {
			$strength = round($enemy->strength * ($level / 3.2)) * (-1);
		} elseif ($param['param'] == 'dexterity') {
			$dex = round($enemy->dexterity * ($level / 3.2)) * (-1);
		} elseif ($param['param'] == 'agility') {
			$agility = round($enemy->agility * ($level / 3.2)) * (-1);
		}

		$enemy->injury = now()->addSeconds($time);
		$enemy->injury_type = $level;
		$enemy->save();

		$enemy->effects()->create([
			'type' => 3,
			'date' => now()->addSeconds($time),
			'strength' => $strength,
			'dexterity' => $dex,
			'agility' => $agility,
		]);

		$message = '<b>' . $enemy['username'] . '</b> получает в бою ';

		if ($level == 1) {
			$message .= 'лёгкую травму';
		} elseif ($level == 2) {
			$message .= 'среднюю травму';
		} elseif ($level == 3) {
			$message .= 'тяжёлую травму';
		} else {
			$message .= 'неизлечимую травму';
		}

		$message .= ' <b style="color: red">' . $param['name'] . '</b> от <b>' . $user['username'] . '</b>, которая очень сильно повлияла на параметр <b>' . __('stats.' . $param['param']) . '</b>';

		ChatService::insertInChat($enemy, $message);

		return true;
	}

	private function wearout(User $user)
	{
		$result = [];

		$slot = $user->getSlot();

		$wearList = $slot->getItems()
			->filter(function (UserItem $item) {
				return $item->type != 12;
			});

		if ($wearList->isNotEmpty()) {
			$rand = random_int(1, count($wearList));
			$rand_wears = $slot->getItems()->random($rand);

			foreach ($rand_wears as $wear) {
				$wear->wearout += 1;
				$wear->save();

				if ($wear->wearout_max <= $wear->wearout) {
					InventoryService::unsetObject($user, $wear->onset);
				}

				$result[] = '<b>' . $wear->title . '</b>';
			}
		}

		if (count($result) > 0) {
			return 'Ваши Вещи приобрели единицу износа: ' . implode(', ', $result);
		}

		return '';
	}

	private function battleResult($type)
	{
		$addexp = 0;

		// Пометили ботам завершение бояи пометим само заверщение боя
		if (!$this->battle->result) {
			User::query()
				->where('rank', 60)
				->whereBelongsTo($this->battle)
				->update([
					'online' => now(),
					'battle_id' => null,
				]);

			$status = 0;

			if ($this->fighter->side == 0) {
				$status = $type;
			} elseif ($type == 1) {
				$status = 1;
			} elseif ($type == 2) {
				$status = 3;
			} elseif ($type == 3) {
				$status = 2;
			}

			if ($status) {
				$this->battle->result = $status;
				$this->battle->save();
			}
		}

		// Закончили битву, пометили что она завершена
		if ($this->battle->status == BattleStatus::ACTIVE) {
			$this->battle->status = BattleStatus::FINISHED;
			$this->battle->save();
		}

		// Поднимаем активность
		if ($this->user->battery * 20 > $this->user->ustal_now + 20) {
			$this->user->ustal_now += 20;
		} else {
			$this->user->ustal_now = $this->user->battery * 20;
		}

		if ($type == 1) {
			$this->user->draws += 1;
		} elseif ($type == 2) {
			$this->user->losses += 1;
		}

		// Для победы расчитываем полученный опыт
		if ($type == 3) {
			$addexp = $this->getExp($this->user);
		}

		/* // Переделать функцию дропа вещей
			if ($opp_stat['battle_drop']){
				$Drop = db::query("SELECT * FROM `battle_drop` WHERE `id` = '".$opp_stat['battle_drop']."'");
				if (db::num_rows($Drop)){
				$Drops = db::fetch($Drop);
				$ch = rand(1, 99);
				if ($ch < $Drops['rand']) $STD = InsertItem( $Drops['name'], $stat['user'] );
				}

			}
		*/

		$addpoints = 0;

		// Если в клане и выиграли, то прибовляем очки клана
		if ($this->user->tribe && $type == 3) {
			$add1 = round($this->fighter->damage / 2);
			$add2 = round($this->fighter->damage * 1.5);

			$addpoints = random_int($add1, $add2);

			$this->user->tribe->points += $addpoints;
			$this->user->tribe->save();
		}

		$addmoney = 0;

		if ($type == 3) {
			if ($this->user->room == 1) {
				if ($this->battle->type == BattleType::DUEL) {
					$addmoney = 0.25 * $this->user->level;
				} elseif ($this->battle->type == BattleType::GROUP) {
					$addmoney = 0.3 * $this->user->level;
				} elseif ($this->battle->type == BattleType::CHAOS) {
					$addmoney = 0.35 * $this->user->level;
				} elseif ($this->battle->type == BattleType::ALIGN) {
					$addmoney = 0.4 * $this->user->level;
				} else {
					$addmoney = 0.25 * $this->user->level;
				}
			} else {
				$addmoney = 0.2 * $this->user->level;
			}
		}

		if ($addmoney > 0) {
			$this->user->credits += $addmoney;
		}

		if ($this->battle->is_blood && $type == 2) {
			$this->user->injury = now()->addHours(3);
		}

		$message = '';

		if ($type != 3) {
			$message = $this->wearout($this->user);
		}

		if ($type == 1) {
			ChatService::insertInChat($this->user, 'К сожалению ваш бой закончился ничьёй. Попытайтесь снова. Нанесено урона: <b><u>' . $this->fighter->damage . ' HP</u></b>.');
		} elseif ($type == 2) {
			ChatService::insertInChat($this->user, 'Ваш бой закончен, Вы проиграли. Нанесено урона: <b><u>' . $this->fighter->damage . ' HP</u></b>.');
		} elseif ($type == 3) {
			ChatService::insertInChat($this->user, 'Вы одержали победу! Нанесено урона: <b><u>' . $this->fighter->damage . ' HP</u></b>. Получено опыта: <b><u>' . $addexp . '</u></b>.' . ($addmoney > 0 ? ' Получена награда: <b><u>' . $addmoney . '</u> золота</b>.' : ''));
		}

		if ($message != '') {
			ChatService::insertInChat($this->user, $message);
		}

		//if ($STD == 1)
		//	$this->game->insertInChat("После боя вы обнаружили <b>" . $Drops['title'] . "</b>. Вы подняли его и положили в рюкзак.", $stat['username'], true);
		if ($addpoints > 0) {
			ChatService::insertInChat($this->user, 'Вы заработали для клана ' . $addpoints . ' очков рейтинга.');
		}

		$this->user->battle()->associate(null);
		$this->user->save();
	}

	// ----- # Функция расчёта опыта # ----- //
	private function getExp(User $user): int
	{
		$addexp = 0;

		$levelup = Level::query()
			->where('level', $user->level)
			->where('up', $user->up)
			->first();

		if ($levelup) {
			$level = Level::query()
				->where('id', $levelup->id + 1)
				->first();

			// ----- # Расчитываем получаемый опыт для физического поединка # ----- //
			if ($this->battle->type == BattleType::DUEL) {
				/** @var BattleMember $enemy */
				$enemy = $this->battle->members
					->where('user_id', '!=', $this->user->id)
					->first();

				$addexp = round($enemy->exp * random_int(1, 1.2));
			} else { // ----- # ... для группового поединка # ----- //
				//include("includes_2/battle/exp.php");
			}

			$addexp *= 2;

			if ($this->battle->type == BattleType::CHAOS) {
				$addexp *= 1.3;
			}

			$maxExp = match ($user->level) {
				7 => 14000,
				8 => 18000,
				9 => 24000,
				10 => 36000,
				11 => 48000,
				12 => 60000,
				default => 12000,
			};

			if ($addexp > $maxExp) {
				$addexp = $maxExp;
			}

			// ----- # Если есть ускорение, то опыта в 2 раза больше # ----- //
			if ($user->sign > time()) {
				$addexp *= 2;
			}
			// ----- # Если есть вип значёк, то опыта в 3 раза больше # ----- //
			if ($user->vip == 1) {
				$addexp *= 3;
			}
			// ----- # Если противник бот, то опыта в 2 раза меньше # ----- //
			//if ($opp_stat['rank'] == 60)
			//	$addexp *= 1;

			$addexp = (int) round($addexp);

			if ($user->exp + $addexp >= $level->exp) {
				$newExp = $user->exp + $addexp;

				$up_level = Level::query()->where('exp', '>', $newExp)
					->orderBy('id')
					->first();

				if ($up_level) {
					$addons = Level::query()
						->select(
							DB::raw('SUM(credits) as credits'),
							DB::raw('SUM(updates) as updates'),
							DB::raw('SUM(raseup) as raseup'),
						)
						->where('id', '>', $levelup->id)
						->where('id', '<=', $up_level->id - 1)
						->toBase()
						->first();

					$ups = Level::query()
						->where('id', $up_level->id - 1)
						->first();

					if ($ups->level > $user->level) {
						ChatService::insertInChat(null, "Персонаж <b>" . $user->name . "</b> получил повышение! Теперь он <b>" . $ups->level . "</b> уровня! Поздравим его с этим достижением.", false);
					}

					$user->wins += 1;
					$user->exp += $addexp;
					$user->level = $ups->level;
					$user->up = $ups->up;

					if ($addons) {
						$user->updates += $addons->updates;
						//$user->o_updates += $addons['raseup'];
						$user->credits += $addons->credits;
					}
				}
			} else {
				$user->wins += 1;
				$user->exp += $addexp;
			}
		}

		return $addexp;
	}

	private function timeout()
	{
		// Выбираем игроков в бою которые не сходили к моменту таймаута
		$sliv = $this->battle->members
			->whereNull('finished_at')
			->whereNull('died_at')
			->filter(function (BattleMember $member) {
				return $member->user->rank != 60 && $member->user->hp_now > 0;
			});

		foreach ($sliv as $enemy) {
			// Помечаем окончание раунда
			if ($this->battle->round > 1) {
				$enemy->exp = $enemy->exp / 2;
				$enemy->died_at = now();
			}

			$enemy->finished_at = now();
			$enemy->save();

			$this->battle->logs()
				->make([
					'round' => $this->battle->round,
					'comment_id' => $this->battle->round > 1 ? 79 : 78,
				])
				->member()->associate($enemy)
				->save();
		}
	}

	private function calcMF($x, $y)
	{
		$MF = 0;

		if (4 * $x <= $y) {
			$MF = 1 - 2 * $x / (5 * $y);
		} elseif (2 * $x <= $y && $y < 4 * $x) {
			$MF = 1.05 - 0.6 * $x / $y;
		} elseif (4 * $x / 3 <= $y && $y < 2 * $x) {
			$MF = 1.75 - 2 * $x / $y;
		} elseif ($x <= $y && $y < 4 * $x / 3) {
			$MF = 0.7 - 0.6 * $x / $y;
		} elseif (2 * $x / 3 <= $y && $y < $x) {
			$MF = 0.28 - 0.18 * $x / $y;
		} elseif ($x / 2 <= $y && $y < 2 * $x / 3) {
			$MF = 0.04 - 0.02 * $x / $y;
		} elseif ($y < $x / 2) {
			$MF = 0;
		}

		return $MF;
	}

	private function calcInjury(User $user, User $opp, $hp, $hpfull)
	{
		if ($hp >= $hpfull * TRAVMA_HARD) {
			return $this->setInjury($user, $opp, 3);
		} elseif ($hp >= $hpfull * TRAVMA_MEDIUM) {
			return $this->setInjury($user, $opp, 2);
		} elseif ($hp >= $hpfull * TRAVMA_LIGHT) {
			return $this->setInjury($user, $opp, 1);
		}

		return false;
	}

	private function kick(BattleLog $user, BattleLog $enemy, int $Round): int
	{
		// uvorot - увеличивает уворот
		// krit - увеличивает критический удар
		// metkost - увеличивает меткость
		// hp - увеличмвает хп
		// mkrit - увеличивает мощность крита
		// pblock - увеличивает пробой блока
		// pbr - увеличивает пробой брони
		// dam - увеличение урона
		$ability = ['uvorot' => 0, 'crit' => 0, 'hp' => 0, 'mkrit' => 0, 'pblock' => 0, 'pbr' => 0, 'damage' => 0, 'antidam' => 0];
		$abilityOpponent = $ability;

		if ($user->member->wait == 1) {
			switch ($user->member->ability) {
				case 1:
					break;
				case 2:
					$ability['damage'] = 35;
					break;
				case 3:
					$ability['crit'] = 1000;
					break;
				case 4:
					$ability['uvorot'] = 1000;
					break;
				case 5:
					$ability['damage'] = 50;
					break;
				case 6:
					$ability['damage'] = 5;
					break;
				case 7:
					$ability['damage'] = 3;
					break;
				case 9:
					$ability['hp'] = 3;
					break;
				case 10:
					$ability['damage'] = 5;
					break;
				case 12:
					$ability['hp'] = 5;
					break;
				case 13:
					$ability['damage'] = 10;
					break;
				case 14:
					$ability['damage'] = 15;
					break;
				case 15:
					$ability['hp'] = 10;
					break;
				case 16:
					$ability['damage'] = 15;
					break;
				case 17:
					$ability['damage'] = 25;
					break;
				case 18:
					$ability['hp'] = 20;
					break;
				case 20:
					$ability['damage'] = 20;
					break;
				case 21:
					$ability['damage'] = 30;
					break;
				case 22:
					$ability['hp'] = 30;
					break;
			}

			$user->member->user->min += $ability['damage'];
			$user->member->user->max += $ability['damage'];
			$user->member->user->krit += $ability['crit'];
			$user->member->user->uv += $ability['uvorot'];
			$user->member->user->mkrit += $ability['mkrit'];
			$user->member->user->pblock += $ability['pblock'];
			$user->member->user->pbr += $ability['pbr'];
		}

		if ($enemy->member->wait == 1) {
			switch ($enemy->member->ability) {
				case 8:
					$abilityOpponent['antidam'] = 3;
					break;
				case 11:
					$abilityOpponent['antidam'] = 5;
					break;
				case 19:
					$abilityOpponent['antidam'] = 10;
					break;
			}

			$enemy->member->user->min += $abilityOpponent['antidam'];
			$enemy->member->user->max += $abilityOpponent['antidam'];
			$enemy->member->user->krit += $abilityOpponent['crit'];
			$enemy->member->user->uv += $abilityOpponent['uvorot'];
			$enemy->member->user->pblock += $abilityOpponent['pblock'];
			$enemy->member->user->pbr += $abilityOpponent['pbr'];
		}

		$userKick = $user->hit ?? [];
		$enemyBlock = $enemy->block ?? [];

		$b = [
			$enemy->member->user->br1,
			$enemy->member->user->br2,
			$enemy->member->user->br3,
			$enemy->member->user->br4,
			$enemy->member->user->br5,
		];

		// Расчёт вероятности нашего уворота
		$x = $user->member->user->agility + $user->member->user->unuv / STATS_VS_MOD;
		$y = $enemy->member->user->agility + $enemy->member->user->uv / STATS_VS_MOD;
		$pu = $this->calcMF($x, $y);

		// Расчёт вероятности нашего крита
		$x = $enemy->member->user->dex + $enemy->member->user->unkrit / STATS_VS_MOD;
		$y = $user->member->user->dex + $user->member->user->krit / STATS_VS_MOD;
		$pi = $this->calcMF($x, $y);

		// Расчёт вероятности пробоя блока
		$x = $enemy->member->user->strength + $enemy->member->user->pblock / STATS_VS_MOD;
		$y = $user->member->user->strength + $user->member->user->mblock / STATS_VS_MOD;
		$pbl = $this->calcMF($x, $y);

		// Расчёт вероятности пробоя брони
		$x = $enemy->member->user->strength + $enemy->member->user->pbr / STATS_VS_MOD;
		$y = $user->member->user->strength + $user->member->user->kbr / STATS_VS_MOD;
		$pbr = $this->calcMF($x, $y);

		$a = random_int(0, PRECESSION) / PRECESSION; // случайное число на (0,1), показывающее, сработал ли уворот в данном случае.
		$rb = random_int(0, PRECESSION) / PRECESSION; // случайное число на (0,1), показывающее, сработал ли крит в данном случае.
		$bpr = random_int(0, PRECESSION) / PRECESSION;

		$kickDamage = [1 => 0, 2 => 0];
		$kickAction = [1 => '', 2 => ''];

		$exp_x = 1;

		// уворот
		if ($pu > $a) {
			$kickAction[1] = 'uvorot';
		} elseif ($pi > $rb) {  // крит
			$kickDamage[1] = random_int(1.5 * ($user->member->user->strength / 3 + $user->member->user->min), 2.5 * ($user->member->user->strength / 1.5 + $user->member->user->max));

			if ($kickDamage[1] < 0) {
				$kickDamage[1] = 0;
			}

			$kickAction[1] = 'crit';

			$exp_x *= 1.2;
		} else {
			for ($i = 1; $i <= count($userKick); $i++) {
				if (isset($userKick[$i - 1]) && $userKick[$i - 1] > 0) {
					$rnd = random_int(0, PRECESSION) / PRECESSION;

					if ($userKick[$i - 1] == $enemyBlock[0] || (isset($enemyBlock[1]) && $userKick[$i - 1] == $enemyBlock[1]) || (isset($enemyBlock[2]) && $userKick[$i - 1] == $enemyBlock[2])) {
						if ($pbl > $rnd) {
							$kickDamage[$i] = random_int(0.5 * ($user->member->user->strength / 3 + $user->member->user->min), 0.75 * ($user->member->user->strength / 1.5 + $user->member->user->max));

							if ($kickDamage[$i] < 0) {
								$kickDamage[$i] = 0;
							}

							$kickAction[$i] = 'prob' . $i;

							$exp_x *= 1.2;
						} else {
							$kickDamage[$i] = 0;
							$kickAction[$i] = 'block' . $i;
						}
					} else {
						if ($pbr > $bpr) {
							$b[$userKick[$i - 1] + 1] = 0;

							$user->member->user->min = ceil($user->member->user->min * 0.5);
							$user->member->user->max = ceil($user->member->user->max * 0.5);

							$exp_x *= 1.2;
						}

						$kickDamage[$i] = random_int(round(($user->member->user->strength / 3 + $user->member->user->min) - $b[$userKick[$i - 1] - 1]), round(($user->member->user->strength / 1.5 + $user->member->user->max) - $b[$userKick[$i - 1] - 1]));

						if ($kickDamage[$i] < 0) {
							$kickDamage[$i] = 0;
						}

						$kickAction[$i] = 'udar';
					}
				}
			}
		}

		$damage = array_sum($kickDamage);

		if ($damage < 0) {
			$damage = 0;
		}

		$add_pr = 0;

		if (!$user->member->user->hp_max) {
			$user->member->user->hp_max = 1;
		}

		$uron = $damage / $user->member->user->hp_max;

		if ($uron > 1) {
			$uron = 1;
		}

		if ($enemy->member->user->rating > $user->member->user->rating) {
			$enemy->member->user->rating = $user->member->user->rating;
		}

		$exp_total = $uron * $this->getBaseExp()[$enemy->member->user->level];
		$exp_total *= 2;
		$exp_total *= $exp_x;

		$comment = 0;

		if ($kickAction[1] == 'uvorot') {
			$comment = random_int(31, 33);
			$add_pr = 1;
		} elseif ($kickAction[1] == 'crit') {
			$comment = random_int(21, 23);
			$add_pr = 2;
		} elseif ($kickAction[1] == 'prob1' && $kickAction[2] != 'prob2') {
			$comment = 41;
			$add_pr = 3;
		} elseif ($kickAction[1] != 'prob1' && $kickAction[2] == 'prob2') {
			$comment = 42;
			$add_pr = 3;
		} elseif ($kickAction[1] == 'prob1' && $kickAction[2] == 'prob2') {
			$comment = 43;
			$add_pr = 3;
		} elseif ($kickAction[1] == 'block1' && $kickAction[2] != 'udar') {
			$comment = random_int(11, 20);
			$add_pr = 4;
		} elseif ($kickAction[1] == "udar" && $kickAction[2] != 'block2') {
			$comment = random_int(1, 4);
		} elseif ($kickAction[1] == 'udar' && $kickAction[2] == 'block2') {
			$comment = random_int(5, 7);
			$add_pr = 4;
		} elseif ($kickAction[1] == 'block1' && $kickAction[2] == 'udar') {
			$comment = random_int(8, 10);
			$add_pr = 4;
		}

		if ($exp_total > 0) {
			$user->member->exp += (int) round($exp_total);
		}
		//if ($user['AttackerFighter'] == $this->BattleFighter['FighterID'])
		//	$this->BattleFighter['exp'] += round($exp_total);

		if ($add_pr == 1) {
			$enemy->member->parry += 1;
		} elseif ($add_pr == 2) {
			$user->member->crits += 1;
		} elseif ($add_pr == 3) {
			$user->member->hits += 1;
		} elseif ($add_pr == 4) {
			$enemy->member->blocks += 1;
		}

		if ($damage >= 10) {
			$user->member->hp += (int) round($damage / 10);
		}

		if ($ability['hp'] > 0) {
			$user->member->user->hp_now += $ability['hp'];
			$user->member->user->hp_now = min($user->member->user->hp_now, $user->member->user->hp_max);
		}

		if ($user->member->wait == 1) {
			$user->member->time -= 1;
			$user->member->time = max($user->member->time, 0);

			if ($user->member->time <= 1) {
				$user->member->wait = 0;
			}
		}

		if ($user->member->wait > 1) {
			$user->member->wait -= 1;
		}

		if ($enemy->member->user->hp - $damage <= 0) {
			$this->calcInjury($user->member->user, $enemy->member->user, $damage, $enemy->member->user->hp_max);
		}

		$enemy->member->damage += $damage;
		$enemy->member->save();

		$enemy->member->user->hp_now = max(0, $enemy->member->user->hp_now - $damage);
		$enemy->member->user->save();

		$user->damage = $damage;
		$user->enemy_block = $enemyBlock;
		$user->comment_id = $comment;
		$user->save();

		return $damage;
	}

	protected function calculateKickAndBlockCount()
	{
		$wears = $this->user->getSlot()->getItems();

		foreach ($wears as $wear) {
			if ($wear->onset == 5 && $wear->type == 5) {
				$this->numBlocks++;
			} elseif ($wear->onset == 5 && $wear->type == 1) {
				$this->numKicks++;
			}
		}
	}

	protected function processKick()
	{
		// Зануляем удары и блоки
		$kick1 = 0;
		$kick2 = 0;
		$block1 = 0;
		$block2 = 0;
		$block3 = 0;

		// Вычисляем цифровые значения ударов и блоков по зонам удара
		if (request()->has('headImpact') && request()->boolean('headImpact')) {
			$kick1 = 1;
		}

		if (request()->has('caseImpact') && request()->boolean('caseImpact')) {
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0) {
				$kick2 = 2;
			} else {
				$kick1 = 2;
			}
		}
		if (request()->has('stomachImpact') && request()->boolean('stomachImpact')) {
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0) {
				$kick2 = 3;
			} else {
				$kick1 = 3;
			}
		}
		if (request()->has('beltImpact') && request()->boolean('beltImpact')) {
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0) {
				$kick2 = 4;
			} else {
				$kick1 = 4;
			}
		}
		if (request()->has('legsImpact') && request()->boolean('legsImpact')) {
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0) {
				$kick2 = 5;
			} else {
				$kick1 = 5;
			}
		}

		if (request()->has('headBlock') && request()->boolean('headBlock')) {
			$block1 = 1;
		}

		if (request()->has('caseBlock') && request()->boolean('caseBlock')) {
			if ($block1 > 0 && $block2 == 0) {
				$block2 = 2;
			} else {
				$block1 = 2;
			}
		}

		if (request()->has('stomachBlock') && request()->boolean('stomachBlock')) {
			if ($block1 > 0 && $block2 == 0) {
				$block2 = 3;
			} elseif ($block1 > 0 && $block2 > 0 && $this->numBlocks == 3 && $block3 == 0) {
				$block3 = 3;
			} else {
				$block1 = 3;
			}
		}

		if (request()->has('beltBlock') && request()->boolean('beltBlock')) {
			if ($block1 > 0 && $block2 == 0) {
				$block2 = 4;
			} elseif ($block1 > 0 && $block2 > 0 && $this->numBlocks == 3 && $block3 == 0) {
				$block3 = 4;
			} else {
				$block1 = 4;
			}
		}

		if (request()->has('legsBlock') && request()->boolean('legsBlock')) {
			if ($block1 > 0 && $block2 == 0) {
				$block2 = 5;
			} elseif ($block1 > 0 && $block2 > 0 && $this->numBlocks == 3 && $block3 == 0) {
				$block3 = 5;
			} else {
				$block1 = 5;
			}
		}

		$enemyId = request()->integer('opponent');

		// ----- # Узнаем, в какой команде, и общие сведения о состоянии боя # ----- //
		// Team - команды в бою (0 - левые и 1 - правые)
		// EndRound - закончил ли ты ход
		// TotalExpa - базовое коллчество опыта от перса

		// ----- # Информация о бое (Из таблицы заявок) # ----- //
		// StartTime - время начала поединка (юникстайм)
		// BattleType - тип поединка (1 - дуэль, 2 - групповой бой, 3 - хаот, 4 - бой склонностей)
		// WeaponUsing - можно ли использовать оружие в бою (1 - рукопашка, 0 - обычный с оружием)
		// IsBlood - кровавый бой
		// Timeout - таймаут хода

		// Если есть у перса жизни и он ещё не ходил, то он может сделать ход
		if ($this->user->hp_now > 0 && !$this->fighter->finished_at && !$this->fighter->died_at) {
			// Если стоит хоть один удар, блок и есть противник
			if ($kick1 > 0 && $block1 > 0 && $enemyId > 0) {
				$enemy = $this->battle->members->where('id', $enemyId)->first();

				if (!$enemy) {
					throw new Exception('Противник не найден');
				}

				// Если противник убит, то он не может быть ударен
				if ($enemy->user->hp_now <= 0) {
					$enemyId = 0;
				}

				if ($enemyId) {
					// Помечаем окончание раунда
					$this->fighter->finished_at = now();
					$this->fighter->save();

					$log = $this->battle->logs()->make();
					$log->round = $this->battle->round;
					$log->hit = array_filter([$kick1, $kick2]);
					$log->block = array_filter([$block1, $block2, $block3]);
					$log->member()->associate($this->fighter);
					$log->enemy()->associate($enemy);
					$log->save();
				}
			}
			// Есть ли у тебя удары и блоки
		}
	}

	protected function checkFinished()
	{
		// Есть ли у тебя жизни
		if ($this->fighter->finished_at) {
			// Выбираем бойцов которые не сходили в бою и живы
			$members = $this->battle->members
				->whereNull('finished_at')
				->whereNull('died_at')
				->filter(function (BattleMember $member) {
					return $member->user->rank != 60 && $member->user->hp_now > 0;
				});

			// Если все сходили, то заканчиваем раунд
			if ($members->isEmpty()) {
				$this->botsHit();

				$this->endRound();

				$this->fighter = $this->battle->members->where('user_id', $this->user->id)->first();
			}

			//if ($this->BattleFighter['wait'] > 0) {
			//	$this->BattleFighter['wait'] -= 1;
			//}
		}
	}

	protected function checkBattleResult()
	{
		if ($this->battle->result) {
			if ($this->battle->result == 1) {
				$this->battleResult(1); // Ничья
			} elseif ($this->battle->result == 2) {
				if ($this->fighter->side == 0) {
					$this->battleResult(2); // Проигрыш
				} else {
					$this->battleResult(3); // Победа
				}
			} elseif ($this->battle->result == 3) {
				if ($this->fighter->side == 0) {
					$this->battleResult(3); // Победа
				} else {
					$this->battleResult(2); // Проигрыш
				}
			}
		} else {
			$users_command = $this->battle->members
				->where('side', $this->fighter->side)
				->whereNull('died_at')
				->filter(function (BattleMember $member) {
					return $member->user->hp_now > 0;
				});

			$enemy_command = $this->battle->members
				->where('side', $this->fighter->side == 1 ? 0 : 1)
				->whereNull('died_at')
				->filter(function (BattleMember $member) {
					return $member->user->hp_now > 0;
				});

			if ($users_command->isEmpty() && $enemy_command->isEmpty()) {
				$this->battleResult(1); // Ничья
			} elseif ($users_command->isEmpty() && $enemy_command->isNotEmpty()) {
				$this->battleResult(2); // Проигрыш
			} elseif ($users_command->isNotEmpty() && $enemy_command->isEmpty()) {
				$this->battleResult(3); // Победа
			} elseif ($users_command->isNotEmpty() && $enemy_command->isNotEmpty()) {
			}
		}
	}

	protected function botsHit()
	{
		$members = $this->battle->members
			->whereNull('finished_at')
			->whereNull('died_at')
			->filter(function (BattleMember $member) {
				return $member->user->rank == 60 && $member->user->hp_now > 0;
			});

		foreach ($members as $member) {
			$opponents = $this->battle->members
				->whereNull('died_at')
				->where('side', $member->side == 0 ? 1 : 0);

			if ($opponents->isEmpty()) {
				continue;
			}

			$opponent = $opponents->random();

			if (!$opponent) {
				continue;
			}

			$member->user->calculate();

			$kick1  = random_int(1, 5);
			$block1 = random_int(1, 5);
			$block2 = random_int(1, 5);

			while ($block1 == $block2) {
				$block2 = random_int(1, 5);
			}

			// Помечаем окончание раунда
			$member->finished_at = now();
			$member->save();

			$log = $this->battle->logs()->make();
			$log->round = $this->battle->round;
			$log->hit = array_filter([$kick1]);
			$log->block = array_filter([$block1, $block2]);
			$log->member()->associate($member);
			$log->enemy()->associate($opponent);
			$log->save();
		}
	}

	protected function getBaseExp(): array
	{
		return [
			0 => 5,
			1 => 10,
			2 => 20,
			3 => 30,
			4 => 60,
			5 => 120,
			6 => 180,
			7 => 300,
			8 => 600,
			9 => 1200,
			10 => 2400,
			11 => 3600,
			12 => 5200,
		];
	}
}
