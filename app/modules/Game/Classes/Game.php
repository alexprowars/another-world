<?php

namespace Game;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Game\Models\User;
use Phalcon\Mvc\User\Component;

/**
 * Class ControllerBase
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
 * @property \Game\Models\User user
 * @property \Sky\Core\Access\Auth auth
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \Phalcon\Registry|\stdClass registry
 */
class Game extends Component
{
	private $message = '';
	private $status = 1;
	private $data = [];
	public $tutorial = [];

	public function insertInChat ($message, $username, $isPrivate = true, $redirect = '')
	{
		$chat = json_decode($this->cache->get("game_chat"), true);

		$this->db->insertAsDict(
			"game_log_chat",
			Array
			(
				'user' => 0,
				'time' => time(),
				'text' => $message
			)
		);

		$lastId = $this->db->lastInsertId();

		$chat[] = array((int) $lastId, time(), '', ($username != '' ? Array((string) $username) : false), (bool) $isPrivate, (string) $message, 0, (string) $redirect);

		$this->cache->save("game_chat", json_encode($chat), 86400);
	}

	public function fight ($user, $type = 1)
	{
		$result = '';

		if ($user == '')
			$result = "Укажите логин!";
		else
		{
			$enemy = User::findFirst((is_numeric($user) ? 'id = '.intval($user).'' : 'username = "'.addslashes($user).'"'));

			if ($enemy->id == $this->user->id)
				$result = "Нападение на самого себя - это уже мазохизм...";
			elseif ($type == 2 && $enemy->rank != 60)
				$result = "Персонаж <u>" . $enemy->username . "</u> не является ботом!";
			elseif ($this->user->travma > time() && $this->user->t_level > 2)
				$result = "С тяжелой травмой в бой нельзя!";
			elseif ($type == 2 && $this->user->level > $enemy->level)
				$result = "Выбери равного или более сильного противника!";
			elseif ($type == 2 && $enemy->room != 2)
				$result = "Для нападния Вам необходимо находится в одной комнате!";
			elseif ($this->user->hp_now < ($this->user->hp_max * 0.33))
				$result = "Вы слишком ослаблены для боя!";
			elseif ($type == 2 && ((time() - $enemy->onlinetime) < 30) && ($enemy->battle == $enemy->last_battle || !$enemy->battle) && $enemy->rank == 60)
				$result = "Бот <u>" . $enemy->username . "</u> еще не восстановил свой уровень жизни!";
			else
			{
				if ($enemy->rank == 60 && $enemy->battle && $enemy->battle != $enemy->last_battle)
				{
					$kol_arr = $this->db->query("SELECT id, onlinetime FROM game_users WHERE battle = " . $enemy->battle . " AND id != " . $enemy->id . "");
					$nn = $kol_arr->numRows();

					if ($nn > 0)
					{
						while ($kol_a = $kol_arr->fetch())
						{
							if ($kol_a['onlinetime'] < (time() - 180))
							{
								$this->db->query("DELETE FROM game_battle_users WHERE BattleID = " . $enemy->battle . " AND FighterID = " . $kol_a['id'] . "");
								$this->db->query("UPDATE game_users SET losses=losses+1, battle=0 WHERE id = '" . $kol_a['id'] . "'");

								$nn--;
							}
						}
					}

					if ($nn == 0)
						$enemy->battle = 0;
				}

				if ($enemy->rank == 60 && ($enemy->battle == $enemy->last_battle || !$enemy->battle))
				{
					$slot = $enemy->getSlot();

					$items = $slot->getItemsId();

					if (count($items))
					{
						$_obj = $this->db->query("SELECT SUM(hp) as hp, SUM(vitality) as vitality FROM game_objects WHERE user_id = '" . $enemy->id . "' AND id IN (".implode(',', $items).") LIMIT 1")->fetch();

						$enemy->vitality += $_obj['vitality'];
						$enemy->hp_max = $enemy->vitality * 5 + $_obj['hp'];
					}
					else
						$enemy->hp_max = $enemy->vitality * 5;

					$enemy->hp_now = $enemy->hp_max;

					$this->db->query("UPDATE game_users SET hp_now = '" . $enemy->hp_now . "', hp_max = '" . $enemy->hp_now . "', battle = 0, onlinetime='" . time() . "' WHERE id = '" . $enemy->id . "'");
					$enemy->battle = 0;
				}

				if ($enemy->battle)
				{
					$prt = $this->db->query("SELECT Team, BattleID FROM game_battle_users WHERE BattleID = " . $enemy->battle . " AND FighterID = " . $enemy->id . "")->fetch();

					$side = ($prt['Team'] == 0 ? 1 : 0);

					$levels = $this->db->query("SELECT base FROM game_levels WHERE level = ".$this->user->level." AND up = ".$this->user->up."");

					$this->db->query("INSERT INTO game_battle_users (BattleID, FighterID, Team, TotalExpa) values ('" . $prt['BattleID'] . "', '" . $this->user->id . "', '" . $side . "', '" . $levels['base'] . "')");
					$this->db->query("INSERT INTO game_battle_log (BattleID, HitTime, AttackerFighter, RedComment) values (" . $prt['BattleID'] . ", '" . time() . "', '" . $this->user->id . "', 74)");
					$this->db->query("UPDATE game_users u, game_battle b SET u.battle=" . $prt['BattleID'] . ", u.side=" . $side . ", b.BattleType='2', b.RaundTime = '" . time() . "' WHERE u.id=".$this->user->id." AND b.BattleID=".$prt['BattleID']."");
				}
				else
				{
					$this->db->query("INSERT INTO game_battle (StartTime, BattleType, RaundTime, Timeout, Status) values (" . time() . ", '1', " . time() . ", '180', 'InProcess')");

					$battleId = $this->db->lastInsertId();

					$levels_my = $this->db->query("SELECT base FROM game_levels WHERE level=".$this->user->level."")->fetch();
					$levels_opp = $this->db->query("SELECT base FROM game_levels WHERE level=".$enemy->level."")->fetch();

					$this->db->query("INSERT INTO game_battle_users (BattleID, FighterID, Team, TotalExpa) values (".$battleId.", '".$this->user->id."', '0', '" . $levels_my['base'] . "')");
					$this->db->query("INSERT INTO game_battle_users (BattleID, FighterID, Team, isBot, TotalExpa) values (".$battleId.", '".$enemy->id."', '1', '".($enemy->rank == 60 ? 1 : 0)."', '" . $levels_opp['base'] . "')");

					$this->db->query("INSERT INTO game_battle_log (BattleID, HitTime, AttackerFighter, RedComment) values (" . $battleId . ", '" . time() . "', '" . $this->user->id . "', 71)");

					$this->db->query("UPDATE game_users SET battle=".$battleId.", side=0 WHERE id='".$this->user->id."'");
					$this->db->query("UPDATE game_users SET battle=".$battleId.", side=1 WHERE id='".$enemy->id."'");
				}

				$this->response->redirect('/battle/');
			}
		}

		return $result;
	}

	public function addToLog ($userId, $action = '', $item = '', $place = '')
	{
		$this->db->insertAsDict(
			"game_log_items",
			array
			(
				'user_id'	=> $userId,
				'action'	=> $action,
				'item'		=> $item,
				'time'		=> time(),
				'place'		=> $place,
			)
		);
	}

	public function useMagic ($objectId, $user)
	{
		$message = '';

		$objectId = intval($objectId);

		if ($user != '')
		{
			$a_where = '';

			if ($this->user->battle > 0)
				$a_where = "(s.i17 = ".$objectId." OR s.i18 = ".$objectId." OR s.i19 = ".$objectId." OR s.i20 = ".$objectId.") AND s.user_id = o.user_id AND";

			$_ex = $this->db->query("SELECT o.id, o.inf, o.tip, o.min, o.otravl FROM game_objects o, game_slots s WHERE " . addslashes($a_where) . " (o.id = '" . $objectId . "' AND o.user_id = '" . $this->user->id . "')");

			// Предмет найден
			if ($object = $_ex->fetch())
			{
				$obj_inf = explode("|", $object['inf']);
				$obj_min = explode("|", $object['min']);

				$iteminfo['name'] = $obj_inf[0];

				// Свиток, эелье или руна
				if ($object['tip'] >= 12 AND $object['tip'] <= 14)
				{
					$enemy = User::findFirst((is_numeric($user) ? 'id = '.intval($user).'' : 'username = "'.addslashes($user).'"'));

					// Юзер найден
					if ($enemy !== false)
					{
						if (!$enemy->isBot())
						{
							if (
									$enemy->authlevel == 3 ||
									$enemy->ma_time < time() ||
									($enemy->ma_time > time() && ($this->user->id == $enemy->id || ($iteminfo['name'] == "healing2" || $iteminfo['name'] == "healing3" || $iteminfo['name'] == "healing1" || $iteminfo['name'] == "healing_m" || $iteminfo['name'] == "voskr"))))
							{
								if ($enemy->isOnline())
								{
									if ($enemy->isFree())
									{
										$this->user->calculateParams();
										$this->user->checkEffects();

										if (
												$obj_min[0] <= $this->user->level &&
												$obj_min[1] <= $this->user->strength &&
												$obj_min[2] <= $this->user->dex &&
												$obj_min[3] <= $this->user->agility &&
												$obj_min[4] <= $this->user->vitality &&
												$obj_min[5] <= $this->user->razum &&
												($obj_min[7] == 0 || ($obj_min[7] != 0 && $this->user->proff == $obj_min[7]))
											)
										{
											$this->setRequestStatus(0);

											// ----- # Читаем свиток # ----- //
											include(ROOT_PATH.'app/includes/magic/list.php');
										}
										else
											$message = "Для чтения данного свитка необходимо владеть определенными навыками!";
									}
									else
										$message = "Персонаж <u>" . $enemy->username . "</u> занят какой то работой!";
								}
								else
									$message = "Персонаж <u>" . $enemy->username . "</u> не в игре!".(date("d.m.Y H:i:s", $enemy->onlinetime))."";
							}
							else
								$message = "Персонаж <u>" . $enemy->username . "</u> находится под защитой от магических атак!";
						}
						else
							$message = "Использование свитков на ботов запрещено!";
					}
					else
						$message = "Персонаж не найден!";
				}
				else
					$message = "Данный предмет использовать невозможно!";
			}
			else
				$message = "Свиток не найден!";
		}
		else
			$message = "Укажите логин персонажа!";

		$this->setRequestStatus(2);

		return $message;
	}

	public function dropMagic ($objectId)
	{
		$object = $this->db->query("SELECT id, inf, tip FROM game_objects WHERE id = ".$objectId."")->fetch();

		if (isset($object['id']))
		{
			$obj_inf = explode("|", $object['inf']);
			$obj_inf[6]++;

			if ($obj_inf[7] == $obj_inf[6])
			{
				// ----- # Удаляем свиток # ----- //
				$this->db->delete("game_objects", "id = ?", [$object['id']]);

				// Определяем в какой слот вставлен свиток
				$e_s = $this->db->query("SELECT `i17` AS `slot17`, `i18` AS `slot18`, `i19` AS `slot19` FROM `game_slots` WHERE `user_id` = '" . $object['user_id'] . "'")->fetch();

				$emp_slot = 0;

				if ($e_s['slot17'] == $object['id'])
					$emp_slot = 17;
				elseif ($e_s['slot18'] == $object['id'])
					$emp_slot = 18;
				elseif ($e_s['slot19'] == $object['id'])
					$emp_slot = 19;

				if ($emp_slot != 0)
					$this->db->query("UPDATE game_slots SET s.i" . $emp_slot . " = 0 WHERE s.user_id = '" . $object['user_id'] . "'");
			}
			else
				$this->db->query("UPDATE `game_objects` SET `inf` = '" . $obj_inf[0] . "|" . $obj_inf[1] . "|" . $obj_inf[2] . "|" . $obj_inf[3] . "|" . $obj_inf[4] . "|" . $obj_inf[5] . "|" . $obj_inf[6] . "|" . $obj_inf[7] . "' WHERE `id` = '" . $object['id'] . "'");
		}
	}

	public function loadGameVariables ()
	{
		if ($this->registry->offsetExists('loaded_variables'))
			return;

		require_once(ROOT_PATH."/app/vars.php");

		/** @var array $stats */

		$this->registry->stats = $stats;
		$this->registry->loaded_variables = true;
	}

	public function setRequestMessage ($message = '')
	{
		$this->message = $message;
	}

	public function getRequestMessage ()
	{
		return $this->message;
	}

	public function setRequestStatus ($status = '')
	{
		$this->status = $status;
	}

	public function getRequestStatus ()
	{
		return $this->status;
	}

	public function setRequestData ($data = [])
	{
		if (is_array($data))
			$this->data = $data;
	}

	public function getRequestData ()
	{
		return $this->data;
	}
}