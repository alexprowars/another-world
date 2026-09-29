<?php

namespace App\Engine\Battle;

use App\Exceptions\Exception;
use App\Http\Resources\BattleLogResource;
use App\Models\BattleLog;
use App\Models\BattleMember;
use App\Models\Level;
use App\Models\User;
use App\Models\UserItem;
use App\Models\Battle as BattleModel;
use App\Services\BattleService;
use App\Services\ChatService;
use Illuminate\Support\Facades\DB;

define('PRECESSION', 100000);
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

	public function init()
	{
	}

	public function show()
	{
		$json = [
			'time' => now()->toAtomString(),
			'action' => 'impactForm',
			'result' => null,
			'opponents' => [],
		];

		// Основные боевые константы
		/** @var array $priem_full */
		include(resource_path('/data/battle.php'));

		$this->user->calculate();
		$this->calculateKickAndBlockCount();

		$logId = request()->integer('lastLogId');

		$abilities = $this->user->abilities()
			->pluck('ability', 'slot');

		$isCurrentRound = request()->integer('round') === $this->battle->round;

		if (!$isCurrentRound && request()->hasAny([
			'ability', 'headImpact', 'caseImpact', 'stomachImpact', 'beltImpact', 'legsImpact',
		])) {
			$json['m'] = 'Раунд уже изменился. Выберите действие заново';
		}

		if ($isCurrentRound && request()->has('ability')) {
			$abilityId = request()->integer('ability');

			$ability = $priem_full[$abilityId] ?? null;

			if ($ability === null) {
				$json['m'] = 'Такого приёма не существует';
			} elseif (!$abilities->contains($abilityId)) {
				$json['m'] = 'Этот приём не выбран у персонажа';
			} else {
				$abilityError = $this->getAbilityError($ability);

				if ($abilityError !== null) {
					$json['m'] = $abilityError;
				} else {
					$this->fighter->ability = $abilityId;
					$this->fighter->wait = $ability['wait'];
					$this->fighter->time = $ability['time'];
					$this->fighter->hits -= $ability['hit'];
					$this->fighter->blocks -= $ability['block'];
					$this->fighter->crits -= $ability['crit'];
					$this->fighter->spirit -= $ability['magic'];
					$this->fighter->parry -= $ability['parry'];
					$this->fighter->hp -= $ability['damage'];
					$this->fighter->save();
				}
			}
		}

		if ($this->battle->status === BattleStatus::ACTIVE && !$this->battle->result) {
			if ($isCurrentRound) {
				$this->processKick();
			}

			$this->checkFinished();
		}

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

			$json['action'] = 'userDead';
			$this->numKicks = 0;
			$this->numBlocks = 0;
		} else {
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

		foreach ($abilities as $slot => $abilityId) {
			$ability = $priem_full[$abilityId] ?? null;

			if ($ability === null) {
				continue;
			}

			$json['abilities']['list']['p_' . $slot] = [
				'id' => $abilityId,
				'n' => $ability['name'],
				'b' => $ability['block'],
				'h' => $ability['hit'],
				'k' => $ability['crit'],
				'm' => $ability['magic'],
				'p' => $ability['parry'],
				'd' => $ability['damage'],
				'a' => $ability['about'],
				'w' => $this->getAbilityError($ability) === null ? 0 : 1,
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

		$json['opponent_id'] = null;
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
		$json['round'] = $this->battle->round;
		$json['timeout_left'] = (int) max(0, $timeout);
		$json['timeout'] = $this->battle->timeout;

		$json['logs'] = [];

		// Не выдаём и более поздние сообщения: иначе курсор пропустит скрытые ходы.
		$pendingLogId = $this->battle->result ? null : $this->battle->logs()
			->where('round', '>=', $this->battle->round)
			->min('id');

		$lastLogs = $this->battle->logs()
			->with(['member', 'member.user', 'enemy', 'enemy.user'])
			->orderByDesc('round')
			->orderByDesc('id')
			->where('id', '>', $logId)
			->when($pendingLogId !== null, fn($query) => $query->where('id', '<', $pendingLogId))
			->get();

		$json['logs'] = BattleLogResource::collection($lastLogs)->resolve();

		return $json;
	}

	private function getAbilityError(array $ability): ?string
	{
		if (
			$this->battle->status !== BattleStatus::ACTIVE
			|| $this->battle->result
			|| $this->fighter->died_at
			|| $this->user->hp_now <= 0
		) {
			return 'Сейчас вы не можете использовать приёмы';
		}

		if ($this->fighter->finished_at) {
			return 'Вы уже завершили ход';
		}

		if ($this->battle->round_at->addSeconds($this->battle->timeout)->isPast()) {
			return 'Время хода истекло';
		}

		if ($this->user->level < $ability['level']) {
			return 'Ваш уровень слишком мал для этого приёма';
		}

		if ($this->fighter->wait > 0) {
			return 'Дождитесь окончания текущего приёма';
		}

		if (
			$this->fighter->hits < $ability['hit']
			|| $this->fighter->blocks < $ability['block']
			|| $this->fighter->crits < $ability['crit']
			|| $this->fighter->spirit < $ability['magic']
			|| $this->fighter->parry < $ability['parry']
			|| $this->fighter->hp < $ability['damage']
		) {
			return 'Недостаточно боевых очков для этого приёма';
		}

		return null;
	}

	private function endRound()
	{
		$logs = $this->battle->logs()
			->with(['member', 'member.user'])
			->where('round', $this->battle->round)
			->orderBy('id')
			->get();

		$logsByMember = $logs->keyBy('member_id');
		$aliveAtRoundStart = $logs
			->filter(fn(BattleLog $log) => !$log->member->died_at && $log->member->user->hp_now > 0)
			->pluck('member_id');

		foreach ($logs as $user) {
			// Погибший от магии до расчёта раунда не выполняет ранее выбранный удар.
			if (!$aliveAtRoundStart->contains($user->member_id)) {
				continue;
			}

			$enemy = $logsByMember->get($user->enemy_id);

			if (!$enemy || $user->member->side === $enemy->member->side) {
				continue;
			}

			$user->member->user->calculate();
			$enemy->member->user->calculate();

			$this->kick($user, $enemy, $this->battle->round);
		}

		$this->battle->round++;
		$this->battle->round_at = now();
		$this->battle->save();

		$this->battle->members->each(function (BattleMember $member) {
			$member->finished_at = null;
			$member->save();
		});

		$this->battle->refresh();
		$this->user->refresh();

		return true;
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

		// Восстанавливаем запас сил
		$this->user->stamina_now = min($this->user->stamina_now + 20, $this->user->vitality * 20);

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

		$wornItems = [];

		if ($type != 3) {
			$wornItems = BattleService::wearout($this->user);
		}

		if ($type == 1) {
			ChatService::insertInChat($this->user, 'К сожалению ваш бой закончился ничьёй. Попытайтесь снова. Нанесено урона: <b><u>' . $this->fighter->damage . ' HP</u></b>.');
		} elseif ($type == 2) {
			ChatService::insertInChat($this->user, 'Ваш бой закончен, Вы проиграли. Нанесено урона: <b><u>' . $this->fighter->damage . ' HP</u></b>.');
		} elseif ($type == 3) {
			ChatService::insertInChat($this->user, 'Вы одержали победу! Нанесено урона: <b><u>' . $this->fighter->damage . ' HP</u></b>. Получено опыта: <b><u>' . $addexp . '</u></b>.' . ($addmoney > 0 ? ' Получена награда: <b><u>' . $addmoney . '</u> золота</b>.' : ''));
		}

		if (!empty($wornItems)) {
			$itemNames = array_map(fn(UserItem $item) => '<b>' . e($item->title) . '</b>', $wornItems);
			$message = 'Ваши вещи приобрели единицу износа: ' . implode(', ', $itemNames);

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

	/** Начисляет опыт и обновляет характеристики пользователя; сохранение выполняет вызывающий код. */
	private function getExp(User $user): int
	{
		$addExp = 0;

		$levelUp = Level::query()
			->where('level', $user->level)
			->where('up', $user->up)
			->first();

		if ($levelUp) {
			// ----- # Расчитываем получаемый опыт для физического поединка # ----- //
			if ($this->battle->type == BattleType::DUEL) {
				/** @var BattleMember $enemy */
				$enemy = $this->battle->members
					->where('user_id', '!=', $user->id)
					->first();

				$addExp = round($enemy->exp * random_int(1, 1.2));
			} else { // ----- # ... для группового поединка # ----- //
				//include("includes_2/battle/exp.php");
			}

			$addExp *= 2;

			if ($this->battle->type == BattleType::CHAOS) {
				$addExp *= 1.3;
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

			if ($addExp > $maxExp) {
				$addExp = $maxExp;
			}

			// ----- # Если есть ускорение, то опыта в 2 раза больше # ----- //
			if ($user->sign > time()) {
				$addExp *= 2;
			}
			// ----- # Если есть вип значёк, то опыта в 3 раза больше # ----- //
			if ($user->vip?->isFuture()) {
				$addExp *= 3;
			}
			// ----- # Если противник бот, то опыта в 2 раза меньше # ----- //
			//if ($opp_stat['rank'] == 60)
			//	$addExp *= 1;

			$addExp = (int) round($addExp);

			$newExp = $user->exp + $addExp;

			$reachedLevel = Level::query()
				->where('id', '>', $levelUp->id)
				->where('exp', '<=', $newExp)
				->orderByDesc('id')
				->first();

			if ($reachedLevel) {
				$addons = Level::query()
					->select(
						DB::raw('SUM(credits) as credits'),
						DB::raw('SUM(updates) as updates'),
					)
					->where('id', '>', $levelUp->id)
					->where('id', '<=', $reachedLevel->id)
					->toBase()
					->first();

				if ($reachedLevel->level > $user->level) {
					ChatService::insertInChat(
						null,
						'Персонаж <b>' . $user->name . '</b> получил повышение! Теперь он <b>'
							. $reachedLevel->level . '</b> уровня! Поздравим его с этим достижением.',
						false,
					);
				}

				$user->level = $reachedLevel->level;
				$user->up = $reachedLevel->up;

				if ($addons) {
					$user->updates += $addons->updates;
					$user->credits += $addons->credits;
				}
			}

			$user->wins += 1;
			$user->exp = $newExp;
		}

		return $addExp;
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
		if ($y == 0) {
			// Равные нулевые характеристики дают тот же шанс, что и равные положительные.
			return $x == 0 ? 0.1 : 0;
		}

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
			return BattleService::setInjury($user, $opp, 3);
		} elseif ($hp >= $hpfull * TRAVMA_MEDIUM) {
			return BattleService::setInjury($user, $opp, 2);
		} elseif ($hp >= $hpfull * TRAVMA_LIGHT) {
			return BattleService::setInjury($user, $opp, 1);
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
			$enemy->member->user->armor1,
			$enemy->member->user->armor2,
			$enemy->member->user->armor3,
			$enemy->member->user->armor4,
			$enemy->member->user->armor5,
		];

		// Расчёт вероятности нашего уворота
		$x = $user->member->user->agility + $user->member->user->unuv / STATS_VS_MOD;
		$y = $enemy->member->user->agility + $enemy->member->user->uv / STATS_VS_MOD;
		$pu = $this->calcMF($x, $y);

		// Расчёт вероятности нашего крита
		$x = $enemy->member->user->dexterity + $enemy->member->user->unkrit / STATS_VS_MOD;
		$y = $user->member->user->dexterity + $user->member->user->krit / STATS_VS_MOD;
		$pi = $this->calcMF($x, $y);

		// Расчёт вероятности пробоя блока
		$x = $enemy->member->user->strength + $enemy->member->user->mblock / STATS_VS_MOD;
		$y = $user->member->user->strength + $user->member->user->pblock / STATS_VS_MOD;
		$pbl = $this->calcMF($x, $y);

		// Расчёт вероятности пробоя брони
		$x = $enemy->member->user->strength + $enemy->member->user->kbr / STATS_VS_MOD;
		$y = $user->member->user->strength + $user->member->user->pbr / STATS_VS_MOD;
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

					if (in_array($userKick[$i - 1], $enemyBlock, true)) {
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
							$b[$userKick[$i - 1] - 1] = 0;

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

			if ($user->member->time == 0) {
				$user->member->wait = 0;
			}
		}

		if ($user->member->wait > 1) {
			$user->member->wait -= 1;
		}

		if ($enemy->member->user->hp - $damage <= 0) {
			$this->calcInjury($user->member->user, $enemy->member->user, $damage, $enemy->member->user->hp_max);
		}

		$enemy->member->save();

		$enemy->member->user->hp_now = max(0, $enemy->member->user->hp_now - $damage);
		$enemy->member->user->save();

		$user->member->damage += $damage;
		$user->member->save();
		$user->member->user->save();

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

		$blocks = [];
		$blockZones = [
			'headBlock' => 1,
			'caseBlock' => 2,
			'stomachBlock' => 3,
			'beltBlock' => 4,
			'legsBlock' => 5,
		];

		foreach ($blockZones as $field => $zone) {
			if (request()->boolean($field)) {
				$blocks[] = $zone;
			}
		}

		if (count($blocks) > $this->numBlocks) {
			throw new Exception('Выбрано больше блоков, чем разрешено');
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
			if ($kick1 > 0 && !empty($blocks) && $enemyId > 0) {
				$enemy = $this->battle->members
					->where('id', $enemyId)
					->where('side', $this->fighter->side == 1 ? 0 : 1)
					->whereNull('died_at')
					->first();

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
					$log->block = $blocks;
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
