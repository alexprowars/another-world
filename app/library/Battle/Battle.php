<?php

namespace App\Battle;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use App\Models\Users;
use Phalcon\Mvc\User\Component;

define('PRECESSION','100000');
// STATS_VS_MOD - параметр, задающий соотношение между статами и модификаторами. 1 стат = r модификаторов.
// STATS_VS_HP - параметр, задающий соотношение между статами и хитпоинтами. 1 стат = hp хитпоинтов.
// DAM_AVE - параметр, задающий соотношение между статами и средним уроном. 1 стат = dam_ave урона.
// ARMOR_AVE - параметр, задающий соотношение между статами и броней. 1 стат = armor_ave урона.
define('STATS_VS_MOD', 5);
define('STATS_VS_HP','6');
define('DAM_AVE','1.33');
define('ARMOR_AVE','30');
// TRAVMA_LIGHT - коэффициент для определения лёгкой травмы
// TRAVMA_MEDIUM - коэффициент для определения средней травмы
// TRAVMA_HARD - коэффициент для определения тяжёлой травмы
define('TRAVMA_LIGHT',1.75);
define('TRAVMA_MEDIUM',2.5);
define('TRAVMA_HARD',3);

/**
 * Class Battle
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Assets\Manager assets
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Session\Adapter\Memcache session
 * @property \Phalcon\Http\Response\Cookies cookies
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Mvc\Router router
 * @property \Phalcon\Cache\Backend\Memcache cache
 * @property \App\Models\Users user
 * @property \App\Auth\Auth auth
 * @property \App\Game game
 */
class Battle extends Component
{
	private $numKicks = 1;
	private $numBlocks = 2;
	private $battleId = 0;

	private $Battles = array();
	private $BattleFighter = array();

	private $injury = array
	(
		// лёгкие
		1 => array
		(
			0 => array('param' => 'strength', 'name' => 'шишка на лбу'),
			1 => array('param' => 'strength', 'name' => 'ушиб коленки'),
			2 => array('param' => 'agility', 'name' => 'фингал под глазом'),
			3 => array('param' => 'agility', 'name' => 'растяжение руки'),
			4 => array('param' => 'dex', 'name' => 'ушиб ВЦ'),
			5 => array('param' => 'dex', 'name' => 'шишка на кулаке'),
		),
		// средние
		2 => array
		(
			0 => array('param' => 'strength', 'name' => 'ушиб коленки второй степени'),
			1 => array('param' => 'strength', 'name' => 'растяжение ВЦ'),
			2 => array('param' => 'agility', 'name' => 'выбитый зуб'),
			3 => array('param' => 'agility', 'name' => 'глубокий порез'),
			4 => array('param' => 'dex', 'name' => 'перелом ключицы'),
			5 => array('param' => 'dex', 'name' => 'отбитые почки'),
		),
		// тяжелые
		3 => array
		(
			0 => array('param' => 'strength', 'name' => 'открытый перелом руки'),
			1 => array('param' => 'strength', 'name' => 'перелом позвоночника'),
			2 => array('param' => 'agility', 'name' => 'открытый перелом ноги'),
			3 => array('param' => 'agility', 'name' => 'разрыв селезёнки'),
			4 => array('param' => 'dex', 'name' => 'множественные порезы'),
			5 => array('param' => 'dex', 'name' => 'выбитый глаз'),
		),
	);

	public function init ()
	{
		$this->Battles			= array();
		$this->BattleFighter	= array();

		$this->numKicks		= 1;
		$this->numBlocks	= 2;
		$this->battleId		= 0;
	}

	public function show()
	{
		$json = array();

		// Основные боевые константы
		/** @var array $priem_full */
		include(__DIR__.'/vars.php');

		// Вычисляем основные параметры надетых на игрока вещей
		$left_wear = $this->updateUserParams($this->user);

		$this->battleId = $this->user->battle;

		$opponent	= $this->request->get('opponent', 'int', 0);
		$logId		= $this->request->get('lastLogId', 'int', 0);

		if ($this->request->has('use_priem') && is_numeric($this->request->get('use_priem')))
		{
			$priem = $this->request->get('use_priem', 'int');

			if (isset($priem_full[$priem]))
				$this->db->query("UPDATE `game_battle_users` SET `priem` = '".$priem."', `wait` = '".$priem_full[$priem]['wait']."', `time` = '".$priem_full[$priem]['time']."', `hit` = `hit` - '".$priem_full[$priem]['hit']."', `block` = `block` - '".$priem_full[$priem]['block']."', `krit` = `krit` - '".$priem_full[$priem]['krit']."', `spirit` = `spirit` - '".$priem_full[$priem]['mag']."', `parry` = `parry` - '".$priem_full[$priem]['parry']."', `hp` = `hp` - '".$priem_full[$priem]['dam']."' WHERE `BattleID` = '".$this->user->battle."' AND `FighterID` = '".$this->user->id."'");
		}

		// Зануляем удары и блоки
		$kick1 = 0;
		$kick2 = 0;
		$block1 = 0;
		$block2 = 0;
		$block3 = 0;

		// Вычисляем цифровые значения ударов и блоков по зонам удара
		if ($this->request->has('headImpact') && $this->request->get('headImpact') == "yes")
			$kick1 = 1;
		if ($this->request->has('caseImpact') && $this->request->get('caseImpact') == "yes")
		{
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0)
				$kick2 = 2;
			else
				$kick1 = 2;
		}
		if ($this->request->has('stomachImpact') && $this->request->get('stomachImpact') == "yes")
		{
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0)
				$kick2 = 3;
			else
				$kick1 = 3;
		}
		if ($this->request->has('beltImpact') && $this->request->get('beltImpact') == "yes")
		{
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0)
				$kick2 = 4;
			else
				$kick1 = 4;
		}
		if ($this->request->has('legsImpact') && $this->request->get('legsImpact') == "yes")
		{
			if ($kick1 > 0 && $this->numKicks == 2 && $kick2 == 0)
				$kick2 = 5;
			else
				$kick1 = 5;
		}

		if ($this->request->has('headBlock') && $this->request->get('headBlock') == "yes")
			$block1 = 1;

		if ($this->request->has('caseBlock') && $this->request->get('caseBlock') == "yes")
		{
			if ($block1 > 0 && $block2 == 0)
				$block2 = 2;
			else
				$block1 = 2;
		}

		if ($this->request->has('stomachBlock') && $this->request->get('stomachBlock') == "yes")
		{
			if ($block1 > 0 && $block2 == 0)
				$block2 = 3;
			elseif ($block1 > 0 && $block2 > 0 && $this->numBlocks == 3 && $block3 == 0)
				$block3 = 3;
			else
				$block1 = 3;
		}

		if ($this->request->has('beltBlock') && $this->request->get('beltBlock') == "yes")
		{
			if ($block1 > 0 && $block2 == 0)
				$block2 = 4;
			elseif ($block1 > 0 && $block2 > 0 && $this->numBlocks == 3 && $block3 == 0)
				$block3 = 4;
			else
				$block1 = 4;
		}

		if ($this->request->has('legsBlock') && $this->request->get('legsBlock') == "yes")
		{
			if ($block1 > 0 && $block2 == 0)
				$block2 = 5;
			elseif ($block1 > 0 && $block2 > 0 && $this->numBlocks == 3 && $block3 == 0)
				$block3 = 5;
			else
				$block1 = 5;
		}

		// ----- # Узнаем, в какой команде, и общие сведения о состоянии боя # ----- //
		// Team - команды в бою (0 - левые и 1 - правые)
		// EndRound - закончил ли ты ход
		// TotalExpa - базовое коллчество опыта от перса

		$this->BattleFighter = $this->db->query("SELECT * FROM `game_battle_users` WHERE `BattleID` = '".$this->battleId."' AND `FighterID` = '".$this->user->id."' LIMIT 1")->fetch();

		// Если не задана команда, то по-умолчанию левая.
		if ($this->BattleFighter['Team'] == '')
			$participant['Team'] = 0;

		// Определяем команду противника (противоположную своей)
		$opp_side = ($this->BattleFighter['Team'] == 1 ? 0 : 1);

		// ----- # Информация о бое (Из таблицы заявок) # ----- //
		// StartTime - время начала поединка (юникстайм)
		// BattleType - тип поединка (1 - дуэль, 2 - групповой бой, 3 - хаот, 4 - бой склонностей)
		// WeaponUsing - можно ли использовать оружие в бою (1 - рукопашка, 0 - обычный с оружием)
		// IsBlood - кровавый бой
		// Timeout - таймаут хода
		$this->Battles = $this->db->query("SELECT * FROM `game_battle` WHERE `BattleID` = '".$this->battleId."' LIMIT 1")->fetch();

		// Если есть у перса жизни и он ещё не ходил, то он может сделать ход
		if ($this->user->hp_now > 0 && $this->BattleFighter['EndRound'] == 0 && $this->BattleFighter['died'] == 0)
		{
			// Если стоит хоть один удар, блок и есть противник
			if ($kick1 > 0 && $block1 > 0 && $opponent > 0)
			{
				// Сходил ли ты?
				$turn = $this->db->query("SELECT `HitID` FROM `game_battle_log` WHERE `BattleID` = '" . $this->battleId . "' AND `AttackerFighter` = '" . $this->user->id . "' AND `HitStatus` = '" . $this->Battles['Raund'] . "'")->fetch();

				if (!isset($turn['HitID']))
				{
					$enemy = $this->db->query("SELECT id, username, hp_now, battle FROM `game_users` WHERE `id` = '" . $opponent . "'")->fetch();

					// Если противник убит, то он не может быть ударен
					if ($enemy['hp_now'] <= 0)
						$opponent = 0;

					if ($opponent > 0)
					{
						// Помечаем окончание раунда
						$this->db->query("UPDATE `game_battle_users` SET `EndRound` = '1' WHERE `BattleID` = '" . $this->battleId . "' AND `FighterID` = '" . $this->user->id . "'");
						$this->BattleFighter['EndRound'] = 1;

						// Запись в бд
						$this->db->query("INSERT INTO `game_battle_log` (BattleID, HitTime, HitStatus, AttackerFighter, AttackerTeam, AttackerHitType, AttackerBlock, DefenderFighter) VALUES (" . $this->battleId . ", " . time() . ", " . $this->Battles['Raund'] . ", '" . $this->user->id . "', '" . $this->BattleFighter['Team'] . "', '" . $kick1 . "," . $kick2 . "', '" . $block1 . "," . $block2 . "," . $block3 . "', '" . $enemy['id'] . "')");
					}
				}
			}
			 // Есть ли у тебя удары и блоки
		}
		// Есть ли у тебя жизни

		if ($this->BattleFighter['EndRound'] == 1)
		{
			// Выбираем бойцов которые не сходили в бою и живы
			$members = $this->db->query("SELECT count(*) AS num FROM `game_battle_users` f, `game_users` u WHERE `f`.BattleID = " . $this->battleId . " AND `f`.`EndRound` = 0 AND `f`.`died` = 0 AND `f`.`isBot` = 0 AND `u`.`id` = `f`.`FighterID` AND `u`.`hp_now` > 0")->fetch()['num'];

			// Если все сходили, то заканчиваем раунд
			if ($members == 0)
			{
				$this->endRound();

				$this->Battles['Raund']++;
				$this->Battles['RaundTime'] = time();

				$this->BattleFighter['EndRound'] = 0;
			}

			if ($this->BattleFighter['wait'] > 0)
				$this->BattleFighter['wait'] -= 1;
		}

		// Вычисляем время таймаута
		$timeout = $this->Battles['Timeout'] - (time() - $this->Battles['RaundTime']);

		$action = array();
		$endbattle = 0;
		$victims = array();
		$random = 0;

		// ----- # HP равно нулю, проигрываем, выигрываем, или ждём окончания боя # ----- //
		if ($this->user->hp_now <= 0 || $this->BattleFighter['died'] == 1 || $this->Battles['Dead'] > 0)
		{
			if ($this->BattleFighter['died'] == 0)
			{
				$this->db->query("UPDATE `game_battle_users` SET `died` = '1' WHERE `BattleID` = '" . $this->battleId . "' AND `FighterID` = '" . $this->user->id . "'");
				$this->BattleFighter['died'] = 1;
			}

			if ($this->Battles['Dead'] > 0)
			{
				if ($this->Battles['Dead'] == 1)
				{
					$action = $this->battleResult(1); // Ничья
					$endbattle = 1;
				}
				elseif ($this->Battles['Dead'] == 2)
				{
					if ($this->BattleFighter['Team'] == 0)
					{
						$action = $this->battleResult(2); // Проигрыш
						$endbattle = 1;
					}
					else
					{
						$action = $this->battleResult(3); // Победа
						$endbattle = 1;
					}
				}
				elseif ($this->Battles['Dead'] == 3)
				{
					if ($this->BattleFighter['Team'] == 0)
					{
						$action = $this->battleResult(3); // Победа
						$endbattle = 1;
					}
					else
					{
						$action = $this->battleResult(2); // Проигрыш
						$endbattle = 1;
					}
				}
			}
			else
			{
				$users_command = $this->db->query("SELECT count(FighterID) AS num FROM game_battle_users bf, game_users p WHERE bf.BattleID = " . $this->battleId . " AND bf.Team = '" . $this->BattleFighter['Team'] . "' AND bf.died = 0 AND p.id = bf.FighterID AND p.hp_now > 0")->fetch()['num'];
				$enemy_command = $this->db->query("SELECT count(FighterID) AS num FROM game_battle_users bf, game_users p WHERE bf.BattleID = " . $this->battleId . " AND bf.Team = '" . $opp_side . "' AND bf.died = 0 AND p.id = bf.FighterID AND p.hp_now > 0")->fetch()['num'];

				if ($users_command == 0 && $enemy_command == 0)
				{
					$action = $this->battleResult(1); // Ничья
					$endbattle = 1;
				}
				elseif ($users_command == 0 && $enemy_command > 0)
				{
					$action = $this->battleResult(2); // Проигрыш
					$endbattle = 1;
				}
				elseif ($users_command > 0 && $enemy_command == 0)
				{
					$action = $this->battleResult(3); // Победа
					$endbattle = 1;
				}
				elseif ($users_command > 0 && $enemy_command > 0)
				{
					$action = array('userDead' , '');
				}
			}
		}
		else
		{
			$json['smena'] = array();

			$accept = 0;

			$n = 0;

			$opponents = $this->db->query("SELECT u.id, u.username FROM `game_battle_users`, `game_users` u WHERE `game_battle_users`.BattleID = " . $this->battleId . " AND `game_battle_users`.Team = '" . $opp_side . "' AND `game_battle_users`.died = 0 AND u.id = `game_battle_users`.FighterID AND u.hp_now > 0");

			// Если в бою есть противники
			if ($opponents->numRows() > 0)
			{
				while ($opponent = $opponents->fetch())
				{
					$victims[$n] = $opponent['id'];

					if ($this->request->has('smena') && $this->request->get('smena', 'int') == $opponent['id'])
						$accept = 1;

					$json['smena'][] = array('id' => $opponent['id'], 'n' => $opponent['username']);

					$n++;
				}

				// Если ты закончил раунд
				if ($this->BattleFighter['EndRound'] == 1)
				{
					// ----------------------------- # Выиграли по таймауту # -------------------------- //
					if ($timeout <= 0 && !$endbattle)
						$action = $this->timeout();
					else
						$action = array('waitImpact', '');
				}
				else
				{
					$random = 0; // rand(0, $n - 1);

					if ($accept == 1)
						$victims[0] = $this->request->get('smena', 'int');

					// ----------------------------- # Проигрыш по таймауту # -------------------------- //
					if ($timeout <= 0 && !$endbattle)
						$action = $this->timeout();
					// --------------------------------- # Конец # ------------------------------------- //

					if ($timeout > 0 && (isset($victims[$random]) || $this->BattleFighter['EndRound'] == 0))
					{
						// Если никого не можеш ударить то удар и блок поставить не можеш
						if (!isset($victims[$random]))
						{
							$this->numBlocks = 0;
							$this->numKicks = 0;
						}
					}
				}
			}
			else
				$action = $this->battleResult(3);
		}

		if (!count($action))
		{
			if ($this->Battles['BattleType'] == 1 && $this->user->room == 2)
				$action = array('impactForm', '');
			else
				$action = array('mapForm', '');
		}

		$json['center'] = array
		(
			'action' => $action[0],
			'is_win' => $action[1],
			'impact' =>
			[
				'stat_kick' 	=> $this->numKicks,
				'stat_block' 	=> $this->numBlocks
			]
		);

		$json['time'] = time();
		$json['m'] = (isset($nms) ? $nms : '');
		$json['priems'] = [];

		$p_block	= 0 + $this->BattleFighter['block'];
		$p_hit		= 0 + $this->BattleFighter['hit'];
		$p_krit		= 0 + $this->BattleFighter['krit'];
		$p_mag		= 0 + $this->BattleFighter['spirit'];
		$p_parry	= 0 + $this->BattleFighter['parry'];
		$p_hp		= 0 + $this->BattleFighter['hp'];

		$userPriem = $this->db->query("SELECT * FROM game_users_priems WHERE user_id = ".$this->user->id)->fetch();

		for ($i = 1; $i <= 10; $i++)
		{
			if ($userPriem[$i] != 0)
			{
				if ($p_block < $priem_full[$userPriem[$i]]['block'] || $p_hit < $priem_full[$userPriem[$i]]['hit'] || $p_krit < $priem_full[$userPriem[$i]]['krit'] || $p_mag < $priem_full[$userPriem[$i]]['mag'] || $p_parry < $priem_full[$userPriem[$i]]['parry'] || $p_hp  < $priem_full[$userPriem[$i]]['dam'] || $this->BattleFighter['wait'] > 0)
					$w = 1;
				else
					$w = 0;

				$json['priems']['p_'.$i] =
				[
					'id' => $userPriem[$i],
					'n' => $priem_full[$userPriem[$i]]['name'],
					'b' => $priem_full[$userPriem[$i]]['block'],
					'h' => $priem_full[$userPriem[$i]]['hit'],
					'k' => $priem_full[$userPriem[$i]]['krit'],
					'm' => $priem_full[$userPriem[$i]]['mag'],
					'p' => $priem_full[$userPriem[$i]]['parry'],
					'd' => $priem_full[$userPriem[$i]]['dam'],
					'a' => $priem_full[$userPriem[$i]]['about'],
					'w' => $w
				];
			}
			else
				$json['priems']['p_'.$i] = ['id' => 0];
		}

		$json['priems']['p'] = array
		(
			'points' => array
			(
				'b' => $p_block,
				'h' => $p_hit,
				'k' => $p_krit,
				'm' => $p_mag,
				'p' => $p_parry,
				'hp' => $p_hp
			),
			'pa' => array
			(
				'w' => $this->BattleFighter['wait'],
				't' => $this->BattleFighter['time'],
				'n' => ($this->BattleFighter['priem'] > 0 ? $priem_full[$this->BattleFighter['priem']]['name'] : '')
			)
		);

		if ($this->user->obraz)
			$obraz = $this->user->obraz;
		else
			$obraz = "1/".$this->user->sex;

		$json['left'] = array
		(
			'user' => array
			(
				'items'	=> $left_wear,
				'data'	=> array
				(
					'orden'			=> $this->user->rank,
					'hp_all'		=> $this->user->hp_max,
					'hp'			=> $this->user->hp_now,
					'energy_all'	=> $this->user->energy_max,
					'energy'		=> $this->user->energy_now,
					'level'			=> $this->user->level,
					'rang'			=> $this->user->tribe,
					'login'			=> $this->user->username,
					'user_id'		=> $this->user->id,
					'obraz'			=> $obraz
				)
			)
		);

		if (!$endbattle)
		{
			$command = array('left' => array(), 'right' => array());

			// Построение комманд
			$fighters = $this->db->query("SELECT f.EndRound, f.Team, u.hp_now as hp, u.level, u.rank, u.username, u.id FROM `game_battle_users` f, `game_users` u WHERE f.BattleID = " . $this->battleId . " AND f.died = '0' AND u.id = f.FighterID AND u.hp_now > 0 ORDER BY u.rank ASC, u.level");

			while ($fighter = $fighters->fetch())
			{
				$fighter['hp'] = floor($fighter['hp']);

				$command[($fighter['Team'] == 0 ? 'left' : 'right')][] = array
				(
					'id'		=> $fighter['id'],
					'login'		=> $fighter['username'],
					'hp'		=> $fighter['hp'],
					'level'		=> $fighter['level'],
					'sd'		=> $fighter['Team'],
					'timeout'	=> $fighter['EndRound'],
				);

				if ($fighter['rank'] == 60 && $fighter['EndRound'] == 0)
				{
					$user_opp = false;

					if ($fighter['Team'] == 0 && count($command['right']))
						$user_opp = $command['right'][rand(0, count($command['right']) - 1)]['id'];
					elseif ($fighter['Team'] == 1 && count($command['left']))
						$user_opp = $command['left'][rand(0, count($command['left']) - 1)]['id'];

					if ($user_opp)
					{
						/**
						 * @var \App\Models\Users $enemy
						 */
						$enemy = Users::findFirst($fighter['id']);

						$this->updateUserParams($enemy, 2);

						$enemy_kick1	= rand(1, 5);
						$enemy_block1	= rand(1, 5);
						$enemy_block2	= rand(1, 5);

						while ($enemy_block1 == $enemy_block2)
						{
							$enemy_block2 = rand(1, 5);
						}

						// Помечаем окончание раунда
						$this->db->query("UPDATE `game_battle_users` SET `EndRound` = '1' WHERE `BattleID` = '" . $this->battleId . "' AND `FighterID` = '" . $enemy->id . "'");

						// Запись в бд
						$this->db->query("INSERT INTO `game_battle_log` (BattleID, HitTime, HitStatus, AttackerFighter, AttackerTeam, AttackerHitType, AttackerBlock, DefenderFighter) VALUES (" . $this->battleId . ", " . time() . ", " . $this->Battles['Raund'] . ", '" . $enemy->id . "', '" . $fighter['Team'] . "', '" . $enemy_kick1 . ",0', '" . $enemy_block1 . "," . $enemy_block2 . ",0', '" . $user_opp . "')");
					}
				}
			}

			$json['action_users'] = array('team_my' => array(), 'team' => array());


			if (isset($command['left']))
				$json['action_users']['team_my'] = $command['left'];

			if (isset($command['right']))
				$json['action_users']['team'] = $command['right'];

			$json['in_battle'] = 'yes';
		}

		if ($timeout > 0 && $this->user->hp_now > 0 && !$endbattle && isset($victims[$random]))
		{
			/**
			 * @var Users $enemy
			 */
			$enemy = Users::findFirst($victims[$random]);

			$right_wear = $this->updateUserParams($enemy, 2);

			if ($enemy->obraz)
				$obraz = $enemy->obraz;
			else
				$obraz = "1/" . $enemy->sex;

			$json['right'] = array
			(
				'enemy' => array
				(
					'items'	=> $right_wear,
					'data'	=> array
					(
						'orden'			=> $enemy->rank,
						'hp_all'		=> $enemy->hp_max,
						'hp'			=> $enemy->hp_now,
						'energy_all'	=> $enemy->energy_max,
						'energy'		=> $enemy->energy_now,
						'level'			=> $enemy->level,
						'obraz'			=> $obraz,
						'rang'			=> $enemy->tribe,
						'login'			=> $enemy->username,
						'user_id'		=> $enemy->id
					)
				),
				'action' => 'getAllEnemy'
			);
		}
		else
			$json['right'] = array('action' => 'getAdvertising');

		$json['logs'] = array();

		$last_turns = $this->db->query("SELECT l.*, u1.username AS AttackerFighterName, u2.username AS DefenderFighterName FROM `game_battle_log` l LEFT JOIN game_users u1 ON u1.id = l.AttackerFighter LEFT JOIN game_users u2 ON u2.id = l.DefenderFighter WHERE l.`BattleID` = '" . $this->battleId . "' AND l.`RedComment` != '0' AND l.`HitStatus` != " . $this->Battles['Raund'] . " AND l.HitID > '" . $logId . "' ORDER BY l.HitID ASC, l.HitStatus DESC");

		if ($last_turns->numRows())
		{
			$lg = 0;

			$i = 0;

			while ($turn = $last_turns->fetch())
			{
				if ($i == 0)
				{
					if ($timeout < 0)
						$timeout = 0;

					$json['info'] = array
					(
						'damage'		=> $this->BattleFighter['damage'],
						'battle_id'		=> $this->user->battle,
						'timebattle'	=> $timeout,
						'timeout'		=> ($this->Battles['Timeout'] / 60)
					);
				}

				if ($i == 0)
					$lg = $turn['HitStatus'];

				$json['logs'][] = array
				(
					'uid'	=> $turn['HitID'],
					'date'	=> date('H:i', $turn['HitTime']),
					'id'	=> $turn['HitStatus'],
					'a'		=> $turn['AttackerFighterName'],
					'at'	=> $turn['AttackerTeam'],
					'ah'	=> $turn['AttackerHitType'],
					'ad'	=> $turn['AttackerDamage'],
					'ab'	=> $turn['AttackerBlock'],
					'd'		=> $turn['DefenderFighterName'],
					'dh'	=> $turn['DefenderHitType'],
					'dd'	=> $turn['DefenderDamage'],
					'db'	=> $turn['DefenderBlock'],
					'c'		=> $turn['RedComment'],
					't'		=> '',
					'my'	=> ($this->user->id == $turn['AttackerFighter'] ||$this->user->id == $turn['DefenderFighter']) ? 'y' : 'n',
					'hr'	=> ($lg < $turn['HitStatus']) ? 1 : 0
				);

				$lg = $turn['HitStatus'];

				$i++;
			}
		}

		return $json;
	}

	private function endRound ()
	{
		$round = $this->db->query("SELECT l.*, f.priem AS usePriem, f.time AS useTime, f.wait AS useWait FROM game_battle_log l, game_battle_users f WHERE l.BattleID = ".$this->battleId." AND f.BattleId = l.BattleID AND f.FighterID = l.AttackerFighter AND l.HitStatus = ".$this->Battles["Raund"]." ORDER BY l.HitID ASC")->fetchAll();

		foreach ($round as $user)
		{
			foreach ($round as $enemy)
			{
				if ($user['AttackerFighter'] == $enemy['DefenderFighter'])
				{
					/**
					 * @var Users $tmp
					 */
					$tmp = Users::findFirst($user['AttackerFighter']);
					$this->updateUserParams($tmp, 2);

					$user['obj'] = $tmp;

					/**
					 * @var Users $tmp
					 */
					$tmp = Users::findFirst($enemy['AttackerFighter']);
					$this->updateUserParams($tmp, 2);

					$enemy['obj'] = $tmp;

					unset($tmp);

					$damage = $this->kick($user, $enemy, $this->Battles['Raund']);

					if ($this->user->id == $enemy['AttackerFighter'])
						$this->user->hp_now -= $damage;
				}
			}
		}

		// Зануляем инфу о раунде и переходим к следующему раунду
		$this->db->query("UPDATE `game_battle` b, `game_battle_users` f SET b.`Raund` = b.`Raund` + 1, b.`RaundTime` = " . time() . ", f.EndRound = '0' WHERE b.`BattleID` = " . $this->battleId . " AND f.`BattleID` = " . $this->battleId . "");

		$this->BattleFighter = $this->db->query("SELECT * FROM `game_battle_users` WHERE `BattleID` = '".$this->battleId."' AND `FighterID` = '".$this->user->id."' LIMIT 1")->fetch();

		return true;
	}

	private function updateUserParams (Users &$user, $userType = 1)
	{
		$slot = $user->getSlot();

		$items = $slot->getItemsId();

		if (count($items))
		{
			// Выбираем все вещи которые одеты на игроке
			$wears = $this->db->query("SELECT * FROM game_objects WHERE user_id = '" . $user->id . "' AND id IN (".implode(',', $items).") ORDER BY onset DESC");

			while ($wear = $wears->fetch())
			{
				// В какой слот одета вещь (могут быть проблемы)
				$i = $wear['onset'];
				if ($i == 16 && !isset($w['4']))
					$i = 4;

				$user->strength		+= $wear['strength'];
				$user->agility		+= $wear['agility'];
				$user->dex			+= $wear['dex'];
				$user->vitality		+= $wear['vitality'];
				$user->razum		+= $wear['razum'];

				$user->hp			+= $wear['hp'];
				$user->energy		+= $wear['energy'];

				$user->br1			+= $wear['br1'];
				$user->br2			+= $wear['br2'];
				$user->br3			+= $wear['br3'];
				$user->br4			+= $wear['br4'];
				$user->br5			+= $wear['br5'];

				$user->krit			+= $wear['krit'];
				$user->mkrit		+= $wear['mkrit'];
				$user->unkrit		+= $wear['unkrit'];
				$user->uv			+= $wear['uv'];
				$user->unuv			+= $wear['unuv'];

				$user->pblock		+= $wear['pblock'];
				$user->mblock		+= $wear['mblock'];
				$user->pbr			+= $wear['pbr'];
				$user->kbr			+= $wear['kbr'];

				$user->min			+= $wear['min_d'];
				$user->max			+= $wear['max_d'];

				if ($userType == 1)
				{
					if ($i == 5 && $wear['tip'] == 5)
						$this->numBlocks++;
					elseif ($i == 5 && $wear['tip'] == 1)
						$this->numKicks++;
				}

				$info = explode("|", $wear['inf']);

				if ($i == 17 || $i == 18)
					$w[$i] = array('i' => $info[0], 'name' => $info[1], 'id' => $wear['id'], 'n' => $info[0], 't' => $info[1], 'type' => $wear['tip']);
				else
					$w[$i] = array('i' => $info[0], 'name' => $info[1], 'type' => $wear['tip']);
			}
		}

		if ($user->rank != 60)
		{
			// Положительные и отрицательные эффекты на персонаже (элики, ауры, проклятья)
			$effects = $this->db->query("SELECT * FROM game_users_effects WHERE user_id = '" . $user->id . "' AND time > " . time());

			while ($effect = $effects->fetch())
			{
				$user->strength	+= $effect['strength'];
				$user->dex		+= $effect['dex'];
				$user->agility	+= $effect['agility'];
				$user->vitality	+= $effect['vitality'];
				$user->power	+= $effect['power'];
				$user->razum	+= $effect['razum'];
				$user->battery	+= $effect['battery'];
				$user->br1		+= $effect['br1'];
				$user->br2		+= $effect['br2'];
				$user->br3		+= $effect['br3'];
				$user->br4		+= $effect['br4'];
				$user->br5		+= $effect['br5'];
				$user->min		+= $effect['min'];
				$user->max		+= $effect['max'];

				$user->effects++;
			}
		}

		$user->hp_now = floor($user->hp_now);

		// HP, Energy
		$user->hp_max		= $user->vitality * 5 + $user->hp;
		$user->energy_max	= ceil(($user->power * 5 + $user->energy));

		$items = array();

		// Для пустых слотов
		for ($i = 1; $i < 23; $i++)
		{
			if (empty($w[$i]))
				$items["s_" . $i] = array('i' => "w" . $i);
			else
				$items["s_" . $i] = $w[$i];
		}

		return $items;
	}

	//	принцип действия: лёгкая травма лишает игрока 1/3 одного из статов
	//	средняя травма заберёт 2/3
	//	тяжёлая - в ноль.
	//	лёгкая травма даёт возможность играть относительно нормально
	//	средняя - только рукопашку и снимает все вещи
	//	тяжёлая - лишает возможности играть и снимает все вещи
	//	две лёгкие = 1 средняя
	//	все остальные комбинации = 1 тяжёлая
	private function setInjury ($userId, $enemyId, $level)
	{
		$user = $this->db->query("SELECT id, username FROM game_users WHERE id = '".$userId."'")->fetch();

		if (!isset($user['id']))
			return false;

		$enemy = $this->db->query("SELECT id, username, rank, travma, strength, dex, agility FROM game_users WHERE id = '".$enemyId."'")->fetch();

		if (!isset($enemy['id']))
			return false;

		if ($enemy['rank'] == 60)
			return false;

		if ($enemy['travma'] > time())
			return false;

		$time = 300 + (300 * $level);

		$param = $this->injury[$level][array_rand($this->injury[$level])];

		$strength = $dex = $agility = 0;

		if ($param['param'] == 'strength')
			$strength = round($enemy['strength'] * ($level / 3.2)) * (-1);
		elseif ($param['param'] == 'dex')
			$dex = round($enemy['dex'] * ($level / 3.2)) * (-1);
		elseif ($param['param'] == 'agility')
			$agility = round($enemy['agility'] * ($level / 3.2)) * (-1);

		$this->db->updateAsDict(
			"game_users",
			[
				'travma' 	=> time() + $time,
				't_level' 	=> $level
			],
			"id = ".$enemy['id']
		);

		$this->db->insertAsDict(
			"game_users_effects",
			[
				'user_id' 	=> $enemy['id'],
				'type' 		=> 3,
				'time'		=> time() + $time,
				'strength'	=> $strength,
				'dex'		=> $dex,
				'agility'	=> $agility
			]
		);

		$message = '<b>'.$enemy['username'].'</b> получает в бою ';

		if ($level == 1)
			$message .= 'лёгкую травму';
		else if ($level == 2)
			$message .= 'среднюю травму';
		else if ($level == 3)
			$message .= 'тяжёлую травму';
		else
			$message .= 'неизлечимую травму';

		$message .= ' <b style="color: red">'.$param['name'].'</b> от <b>'.$user['username'].'</b>, которая очень сильно повлияла на параметр <b>'._getText('stats', $param['param']).'</b>';

		$this->game->insertInChat($message, $enemy['username'], true);

		return true;
	}

	private function wearout (Users $user)
	{
		$result = array();

		$slot = $user->getSlot();

		$wearList = $slot->getItemsId();

		if (count($wearList) > 0)
		{
			$rand = mt_rand(1, count($wearList));
			$rand_wears = array_rand($wearList, $rand);

			if (!is_array($rand_wears))
				$rand_wears = array($rand_wears);

			foreach ($rand_wears as $key)
			{
				if ($object = $this->db->query("SELECT id, inf, onset FROM game_objects WHERE user_id = '" . $user->id . "' AND tip != 12 AND id = " . $wearList[$key])->fetch())
				{
					$info = explode("|", $object['inf']);
					$info[6]++;

					$result[] = "<b>" . $info[1] . "</b>";

					// --- # Добавление износа # --- //
					if ($info[7] <= $info[6])
					{
						$info[3] = 0;
						$this->db->query("UPDATE game_objects SET inf = '" . implode('|', $info) . "', `onset` = 0 WHERE id = '" . $object['id'] . "'");

						$slot->unsetObject($object['onset']);
					}
					else
						$this->db->query("UPDATE game_objects SET inf = '" . implode('|', $info) . "' WHERE id = '" . $object['id'] . "'");
				}
			}
		}

		if (count($result) > 0)
			return "Ваши Вещи приобрели единицу износа: " . implode(', ', $result);

		return '';
	}

	private function battleResult ($type)
	{
		global $addexp;

		$update = array();

		// Пометили ботам завершение бояи пометим само заверщение боя
		if ($this->Battles['Dead'] == 0)
		{
			$this->db->query("UPDATE game_users SET last_battle = " . $this->battleId . ", onlinetime = " . time() . " WHERE battle = '" . $this->battleId . "' AND `rank` = 60");

			$status = 0;

			if ($this->BattleFighter['Team'] == 0)
				$status = $type;
			else
			{
				if ($type == 1)
					$status = 1;
				elseif ($type == 2)
					$status = 3;
				elseif ($type == 3)
					$status = 2;
			}

			if ($status > 0)
				$this->db->query("UPDATE `game_battle` SET `Dead` = " . $status . " WHERE `BattleID` = " . $this->battleId . "");
		}

		// Закончили битву, пометили что она завершена
		if ($this->Battles['Status'] == 'InProcess')
			$this->db->query("UPDATE game_battle SET Status = 'Finished' WHERE BattleID = '" . $this->battleId . "'");

		// Поднимаем активность
		if ($this->user->battery * 20 > $this->user->ustal_now + 20)
			$update['ustal_now'] = $this->user->ustal_now + 20;
		else
			$update['ustal_now'] = $this->user->battery * 20;

		if ($this->session->has('auto_go'))
			$this->session->remove('auto_go');

		if ($type == 1)
			$update['drawn'] = $this->user->drawn + 1;
		elseif ($type == 2)
			$update['losses'] = $this->user->losses + 1;

		// Для победы расчитываем полученный опыт
		if ($type == 3)
			$update += $this->getExp($this->user);

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
		if ($this->user->tribe > 0 && $type == 3)
		{
			$add1 = round($this->BattleFighter['damage'] / 2);
			$add2 = round($this->BattleFighter['damage'] * 1.5);

			$addpoints = rand($add1, $add2);

			$this->db->query("UPDATE game_tribes SET points = points + ".rand($add1, $add2)." WHERE id = '" . $this->user->tribe . "'");
		}

		$addmoney = 0;

		if ($type == 3)
		{
			if ($this->user->room == 1)
			{
				if ($this->Battles['BattleType'] == 1)
					$addmoney = 0.25 * $this->user->level;
				elseif ($this->Battles['BattleType'] == 2)
					$addmoney = 0.3 * $this->user->level;
				elseif ($this->Battles['BattleType'] == 3)
					$addmoney = 0.35 * $this->user->level;
				elseif ($this->Battles['BattleType'] == 4)
					$addmoney = 0.4 * $this->user->level;
				else
					$addmoney = 0.25 * $this->user->level;
			}
			else
				$addmoney = 0.2 * $this->user->level;
		}

		if ($addmoney > 0)
			$update['credits'] = $this->user->credits + $addmoney;

		if ($this->Battles['IsBlood'] == 1 && $type == 2)
			$update['travma'] = time() + 10800;

		$message = '';

		if ($type != 3)
			$message = $this->wearout($this->user);

		if ($type == 1)
			$this->game->insertInChat("К сожалению ваш бой закончился ничьёй. Попытайтесь снова. Нанесено урона: <b><u>" . $this->BattleFighter['damage'] . " HP</u></b>.", $this->user->username, true);
		elseif ($type == 2)
			$this->game->insertInChat("Ваш бой закончен, Вы проиграли. Нанесено урона: <b><u>" . $this->BattleFighter['damage'] . " HP</u></b>.", $this->user->username, true);
		elseif ($type == 3)
			$this->game->insertInChat("Вы одержали победу! Нанесено урона: <b><u>" . $this->BattleFighter['damage'] . " HP</u></b>. Получено опыта: <b><u>" . $addexp . "</u></b>.".($addmoney > 0 ? " Получена награда: <b><u>" . $addmoney . "</u> золота</b>." : ""), $this->user->username, true);
		if ($message != '')
			$this->game->insertInChat($message, $this->user->username, true);
		//if ($STD == 1)
		//	$this->game->insertInChat("После боя вы обнаружили <b>" . $Drops['title'] . "</b>. Вы подняли его и положили в рюкзак.", $stat['username'], true);
		if ($addpoints > 0)
			$this->game->insertInChat("Вы заработали для клана " . $addpoints . " очков рейтинга.", $this->user->username, true);

		$update['battle'] = 0;

		$this->db->updateAsDict(
			"game_users",
			$update,
			"id = ".$this->user->id
		);

		if ($type == 1)
			return array('finishBattle', 'draw');
		elseif ($type == 2)
			return array('finishBattle', 'no');
		elseif ($type == 3)
			return array('finishBattle', 'yes');
		else
			return array();
	}

	// ----- # Функция расчёта опыта # ----- //
	private function getExp (Users $user)
	{
		global $addexp;

		$result = array();

		$levelup = $this->db->query("SELECT `id` FROM game_levels WHERE level = " . $user->level . " AND up = " . $user->up . "")->fetch();

		if (isset($levelup['id']))
		{
			$level = $this->db->query("SELECT `exp` FROM game_levels WHERE id = " . $levelup['id'] . " + 1")->fetch();

			$addexp = 0;

			// ----- # Расчитываем получаемый опыт для физического поединка # ----- //
			if ($this->Battles['BattleType'] == 1)
			{
				$singleBattle = $this->db->query("SELECT `TotalExpa` FROM game_battle_users WHERE game_battle_users.BattleID = '" . $this->battleId . "' AND game_battle_users.FighterID != '" . $user->id . "'")->fetch();

				$addexp = round($singleBattle['TotalExpa'] * rand(1, 1.2));
			}
			// ----- # ... для группового поединка # ----- //
			elseif ($this->Battles['BattleType'] == 2 || $this->Battles['BattleType'] == 3 || $this->Battles['BattleType'] == 4)
			{
				//include("includes_2/battle/exp.php");
			}

			$addexp *= 2;

			if ($this->Battles['BattleType'] == 3)
				$addexp *= 1.3;

			switch ($user->level)
			{
				case 7:
					$maxExp = 14000;
					break;
				case 8:
					$maxExp = 18000;
					break;
				case 9:
					$maxExp = 24000;
					break;
				case 10:
					$maxExp = 36000;
					break;
				case 11:
					$maxExp = 48000;
					break;
				case 12:
					$maxExp = 60000;
					break;
				default:
					$maxExp = 12000;
					break;
			}
			if ($addexp > $maxExp)
				$addexp = $maxExp;

			// ----- # Если есть ускорение, то опыта в 2 раза больше # ----- //
			if ($user->sign > time())
				$addexp *= 2;
			// ----- # Если есть вип значёк, то опыта в 3 раза больше # ----- //
			if ($user->vip == 1)
				$addexp *= 3;
			// ----- # Если противник бот, то опыта в 2 раза меньше # ----- //
			//if ($opp_stat['rank'] == 60)
			//	$addexp *= 1;

			$addexp = round($addexp);

			if ($user->exp + $addexp >= $level['exp'])
			{
				$newExp = $user->exp + $addexp;

				$up_level = $this->db->query("SELECT * FROM game_levels WHERE exp > " . $newExp . " ORDER BY id LIMIT 1")->fetch();

				if (isset($up_level))
				{
					$addons = $this->db->query("SELECT SUM(credits) as credits, SUM(updates) as updates, SUM(raseup) as raseup FROM game_levels WHERE id > " . $levelup['id'] . " AND id <= " . $up_level['id'] . "-1")->fetch();

					$ups = $this->db->query("SELECT level, up FROM game_levels WHERE id = " . $up_level['id'] . "-1")->fetch();

					if ($ups['level'] > $user->level)
						$this->game->insertInChat("Персонаж <b>" . $user->username . "</b> получил повышение! Теперь он <b>" . $ups['level'] . "</b> уровня! Поздравим его с этим достижением.", "", false);

					$result['wins']		= $user->wins + 1;
					$result['exp']		= $user->exp + $addexp;
					$result['level']	= $ups['level'];
					$result['up']		= $ups['up'];

					if (isset($addons['credits']))
					{
						$result['s_updates']	= $user->s_updates + $addons['updates'];
						$result['o_updates']	= $user->o_updates + $addons['raseup'];
						$result['credits']		= $user->credits + $addons['credits'];
					}
				}
			}
			else
			{
				$result['wins'] = $user->wins + 1;
				$result['exp'] 	= $user->exp + $addexp;
			}
		}

		return $result;
	}

	private function timeout ()
	{
		// Выбираем игроков в бою которые не сходили к моменту таймаута
		$sliv = $this->db->query("SELECT f.Team, f.TotalExpa, u.* FROM game_battle_users f, game_users u WHERE f.BattleID = " . $this->battleId . " AND f.EndRound = 0 AND f.died = 0 AND f.isBot = 0 AND u.id = f.FighterID AND u.hp_now > 0");

		while ($enemy = $sliv->fetch())
		{
			if ($this->Battles['Raund'] > 1)
			{
				// Помечаем окончание раунда
				$this->db->query("UPDATE `game_battle_users` SET `EndRound` = '1', `died` = '1', `TotalExpa` = '" . ($enemy['TotalExpa'] / 2) . "' WHERE `BattleID` = '" . $this->battleId . "' AND `FighterID` = '" . $enemy['id'] . "'");
			}
			else
			{
				// Помечаем окончание раунда
				$this->db->query("UPDATE `game_battle_users` SET `EndRound` = '1' WHERE `BattleID` = '" . $this->battleId . "' AND `FighterID` = '" . $enemy['id'] . "'");
			}

			$this->db->query("INSERT INTO `game_battle_log` (BattleID, HitTime, HitStatus, AttackerFighter, AttackerTeam, RedComment) VALUES (" . $this->battleId . ", " . time() . ", " . $this->Battles['Raund'] . ", '" . $enemy['id'] . "', '" . $enemy['Team'] . "', '".(($this->Battles['Raund'] > 1) ? 79 : 78)."')");
		}

		return array('refresh', '');
	}

	private function calcMF ($x, $y)
	{
		$MF = 0;

		if (4 * $x <= $y)
			$MF = 1 - 2 * $x / (5 * $y);
		elseif (2 * $x <= $y && $y < 4 * $x)
			$MF = 1.05 - 0.6 * $x / $y;
		elseif (4 * $x / 3 <= $y && $y < 2 * $x)
			$MF = 1.75 - 2 * $x / $y;
		elseif ($x <= $y && $y < 4 * $x / 3)
			$MF = 0.7 - 0.6 * $x / $y;
		elseif (2 * $x / 3 <= $y && $y < $x)
			$MF = 0.28 - 0.18 * $x / $y;
		elseif ($x / 2 <= $y && $y < 2 * $x / 3)
			$MF = 0.04 - 0.02 * $x / $y;
		elseif ($y < $x / 2)
			$MF = 0;

		return $MF;
	}

	private function calcInjury ($user, $opp, $hp, $hpfull)
	{
		if ($hp >= $hpfull*TRAVMA_HARD)
			return $this->setInjury($user, $opp, 3);
		elseif ($hp >= $hpfull*TRAVMA_MEDIUM)
			return $this->setInjury($user, $opp, 2);
		elseif ($hp >= $hpfull*TRAVMA_LIGHT)
			return $this->setInjury($user, $opp, 1);

		return false;
	}

	private function kick ($user, $enemy, $Round)
	{
		global $base_exp;

		// uvorot - увеличивает уворот
		// krit - увеличивает критический удар
		// metkost - увеличивает меткость
		// hp - увеличмвает хп
		// mkrit - увеличивает мощность крита
		// pblock - увеличивает пробой блока
		// pbr - увеличивает пробой брони
		// dam - увеличение урона
		$priem = array('uvorot' => 0, 'krit' => 0, 'hp' => 0, 'mkrit' => 0, 'pblock' => 0, 'pbr' => 0, 'dam' => 0, 'antidam' => 0);
		$priem_opp = $priem;

		if ($user['useWait'] == 1)
		{
			switch ($user['usePriem'])
			{
				case 1:
					break;
				case 2:
					$priem['dam'] = 35;
					break;
				case 3:
					$priem['krit'] = 1000;
					break;
				case 4:
					$priem['uvorot'] = 1000;
					break;
				case 5:
					$priem['dam'] = 50;
					break;
				case 6:
					$priem['dam'] = 5;
					break;
				case 7:
					$priem['dam'] = 3;
					break;
				case 9:
					$priem['hp'] = 3;
					break;
				case 10:
					$priem['dam'] = 5;
					break;
				case 12:
					$priem['hp'] = 5;
					break;
				case 13:
					$priem['dam'] = 10;
					break;
				case 14:
					$priem['dam'] = 15;
					break;
				case 15:
					$priem['hp'] = 10;
					break;
				case 16:
					$priem['dam'] = 15;
					break;
				case 17:
					$priem['dam'] = 25;
					break;
				case 18:
					$priem['hp'] = 20;
					break;
				case 20:
					$priem['dam'] = 20;
					break;
				case 21:
					$priem['dam'] = 30;
					break;
				case 22:
					$priem['hp'] = 30;
					break;
			}

			$user['obj']->min		+= $priem['dam'];
			$user['obj']->max		+= $priem['dam'];
			$user['obj']->krit		+= $priem['krit'];
			$user['obj']->uv		+= $priem['uvorot'];
			$user['obj']->mkrit		+= $priem['mkrit'];
			$user['obj']->pblock	+= $priem['pblock'];
			$user['obj']->pbr		+= $priem['pbr'];
		}

		if ($enemy['useWait'] == 1)
		{
			switch ($enemy['usePriem'])
			{
				case 8:
					$priem_opp['antidam'] = 3;
					break;
				case 11:
					$priem_opp['antidam'] = 5;
					break;
				case 19:
					$priem_opp['antidam'] = 10;
					break;
			}

			$enemy['obj']->min		+= $priem_opp['antidam'];
			$enemy['obj']->max		+= $priem_opp['antidam'];
			$enemy['obj']->krit		+= $priem_opp['krit'];
			$enemy['obj']->uv		+= $priem_opp['uvorot'];
			$enemy['obj']->pblock	+= $priem_opp['pblock'];
			$enemy['obj']->pbr		+= $priem_opp['pbr'];
		}

		$userKick 	= explode(",", $user['AttackerHitType']);
		$enemyBlock = explode(",", $enemy['AttackerBlock']);

		$b = array($enemy['obj']->br1, $enemy['obj']->br2, $enemy['obj']->br3, $enemy['obj']->br4, $enemy['obj']->br5);

		// Расчёт вероятности нашего уворота
		$x = $user['obj']->agility + $user['obj']->unuv / STATS_VS_MOD;
		$y = $enemy['obj']->agility + $enemy['obj']->uv / STATS_VS_MOD;
		$pu = $this->calcMF($x, $y);

		// Расчёт вероятности нашего крита
		$x = $enemy['obj']->dex + $enemy['obj']->unkrit / STATS_VS_MOD;
		$y = $user['obj']->dex + $user['obj']->krit / STATS_VS_MOD;
		$pi = $this->calcMF($x, $y);

		// Расчёт вероятности пробоя блока
		$x = $enemy['obj']->strength + $enemy['obj']->pblock / STATS_VS_MOD;
		$y = $user['obj']->strength + $user['obj']->mblock / STATS_VS_MOD;
		$pbl = $this->calcMF($x, $y);

		// Расчёт вероятности пробоя брони
		$x = $enemy['obj']->strength + $enemy['obj']->pbr / STATS_VS_MOD;
		$y = $user['obj']->strength + $user['obj']->kbr / STATS_VS_MOD;
		$pbr = $this->calcMF($x, $y);

		$a		= mt_rand(0, PRECESSION) / PRECESSION; // случайное число на (0,1), показывающее, сработал ли уворот в данном случае.
		$rb		= mt_rand(0, PRECESSION) / PRECESSION; // случайное число на (0,1), показывающее, сработал ли крит в данном случае.
		$bpr	= mt_rand(0, PRECESSION) / PRECESSION;

		$kickDamage = array(1 => 0, 2 => 0);
		$kickAction = array(1 => '', 2 => '');

		$exp_x = 1;

		// уворот
		if ($pu > $a)
			$kickAction[1] = "uvorot";
		// крит
		elseif ($pi > $rb)
		{
			$kickDamage[1] = rand(1.5 * ($user['obj']->strength / 3 + $user['obj']->min), 2.5 * ($user['obj']->strength / 1.5 + $user['obj']->max));

			if ($kickDamage[1] < 0)
				$kickDamage[1] = 0;

			$kickAction[1] = "krit";

			$exp_x *= 1.2;
		}
		else
		{
			for ($i = 1; $i <= count($userKick); $i++)
			{
				if (isset($userKick[$i - 1]) && $userKick[$i - 1] > 0)
				{
					$rnd = mt_rand(0, PRECESSION) / PRECESSION;

					if ($userKick[$i - 1] == $enemyBlock[0] || $userKick[$i - 1] == $enemyBlock[1] || (isset($enemyBlock[2]) && $userKick[$i - 1] == $enemyBlock[2]))
					{
						if ($pbl > $rnd)
						{
							$kickDamage[$i] = rand(0.5 * ($user['obj']->strength / 3 + $user['obj']->min), 0.75 * ($user['obj']->strength / 1.5 + $user['obj']->max));

							if ($kickDamage[$i] < 0)
								$kickDamage[$i] = 0;

							$kickAction[$i] = "prob".$i;

							$exp_x *= 1.2;
						}
						else
						{
							$kickDamage[$i] = 0;
							$kickAction[$i] = "block".$i;
						}
					}
					else
					{
						if ($pbr > $bpr)
						{
							$b[$userKick[$i - 1] + 1] = 0;

							$user['obj']->min = ceil($user['obj']->min * 0.5);
							$user['obj']->max = ceil($user['obj']->max * 0.5);

							$exp_x *= 1.2;
						}

						$kickDamage[$i] = rand(round(($user['obj']->strength / 3 + $user['obj']->min) - $b[$userKick[$i - 1] - 1]), round(($user['obj']->strength / 1.5 + $user['obj']->max) - $b[$userKick[$i - 1] - 1]));

						if ($kickDamage[$i] < 0)
							$kickDamage[$i] = 0;

						$kickAction[$i] = "udar";
					}
				}
			}
		}

		$damage = array_sum($kickDamage);

		if ($damage < 0)
			$damage = 0;

		$add_pr = 0;

		if (!$user['obj']->hp_max)
			$user['obj']->hp_max = 1;

		$uron = $damage / $user['obj']->hp_max;

		if ($uron > 1)
			$uron = 1;

		if ($enemy['obj']->reit > $user['obj']->reit)
			$enemy['obj']->reit = $user['obj']->reit;

		$exp_total = $uron * $base_exp[$enemy['obj']->level];
		$exp_total *= 2;
		$exp_total *= $exp_x;

		$comment = 0;

		if ($kickAction[1] == "uvorot")
		{
			$comment = rand(31, 33);
			$add_pr = 1;
		}
		elseif ($kickAction[1] == "krit")
		{
			$comment = rand(21, 23);
			$add_pr = 2;
		}
		elseif ($kickAction[1] == "prob1" && $kickAction[2] != "prob2")
		{
			$comment = 41;
			$add_pr = 3;
		}
		elseif ($kickAction[1] != "prob1" && $kickAction[2] == "prob2")
		{
			$comment = 42;
			$add_pr = 3;
		}
		elseif ($kickAction[1] == "prob1" && $kickAction[2] == "prob2")
		{
			$comment = 43;
			$add_pr = 3;
		}
		elseif ($kickAction[1] == "block1" && $kickAction[2] != "udar")
		{
			$comment = rand(11, 20);
			$add_pr = 4;
		}
		elseif ($kickAction[1] == "udar" && $kickAction[2] != "block2")
		{
			$comment = rand(1, 4);
		}
		elseif ($kickAction[1] == "udar" && $kickAction[2] == "block2")
		{
			$comment = rand(5, 7);
			$add_pr = 4;
		}
		elseif ($kickAction[1] == "block1" && $kickAction[2] == "udar")
		{
			$comment = rand(8, 10);
			$add_pr = 4;
		}

		$str_pr = '';

		if ($exp_total > 0)
			$str_pr .= ", bf.exp = bf.exp + " . round($exp_total);
		//if ($user['AttackerFighter'] == $this->BattleFighter['FighterID'])
		//	$this->BattleFighter['exp'] += round($exp_total);

		if ($add_pr == 1)
			$str_pr .= ', bf2.parry = bf2.parry+1';
		elseif ($add_pr == 2)
			$str_pr .= ', bf.krit = bf.krit+1';
		elseif ($add_pr == 3)
			$str_pr .= ', bf.hit = bf.hit+1';
		elseif ($add_pr == 4)
			$str_pr .= ', bf2.block = bf2.block+1';

		if ($damage >= 10)
			$str_pr .= ', bf.hp = bf.hp+' . round($damage / 10) . '';

		if ($priem['hp'] > 0 && $user['obj']->hp_max > 10)
			$str_pr .= ', p2.hp_now = if(' . $user['obj']->hp_max . '<p2.hp_now+' . $priem["hp"] . ',' . $user['obj']->hp_max . ',p2.hp_now+' . $priem["hp"] . ')';

		if ($user['useWait'] == 1 && $user['useTime'] > 1)
			$str_pr .= ', bf.time=if(bf.time<2,1,bf.time-1)';
		else
			$str_pr .= ', bf.wait=if(bf.wait<1,0,bf.wait-1)';

		if ($enemy['obj']->hp - $damage <= 0)
		{
			$this->calcInjury($user['obj']->id, $enemy['obj']->id, $damage, $enemy['obj']->hp_max);
		}

		$this->db->query("UPDATE `game_users` p, `game_users` p2, `game_battle_users` bf, `game_battle_users` bf2 SET p.hp_now = if(p.hp_now<".$damage.",0,p.hp_now-".$damage."), bf.damage = bf.damage+" . $damage . "" . $str_pr . " WHERE p.id = '" . $enemy['obj']->id . "' AND bf.BattleID = '" . $this->battleId . "' AND bf2.BattleID = bf.BattleID AND bf2.FighterID = p.id AND p2.id = '" . $user['obj']->id . "' AND bf.FighterID = p2.id");
		$this->db->query("UPDATE `game_battle_log` SET `AttackerDamage` = " . $damage . ", `DefenderBlock` = '" . $enemyBlock[0] . "," . $enemyBlock[1] . "" . (isset($enemyBlock[2]) ? ','.$enemyBlock[2] : '') . "', `RedComment` = " . $comment . " WHERE `BattleID` = " . $this->battleId . " AND `HitStatus` = '" . $Round . "' AND `AttackerFighter` = '" . $user['obj']->id . "'");

		return $damage;
	}
}

?>