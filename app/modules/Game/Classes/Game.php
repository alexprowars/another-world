<?php

namespace Game;

use App\Models\User;
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
 * @property \App\Models\User user
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
										$this->user->calculateWearsStats();
										$this->user->calculate();

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
											include(ROOT_PATH.'/app/includes/magic/list.php');
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
}