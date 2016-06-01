<?php

namespace App\Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class TribeController extends Application
{
	private $tribe = [];

	public function initialize ()
	{
		$this->tag->setTitle('Клан');

		parent::initialize();

		$this->tribe = $this->db->query("SELECT * from game_tribes WHERE id = '".$this->user->tribe."'")->fetch();
	}

	private function ld_m ($t, $u, $w, $r, $m, $s)
	{
		$this->db->query("INSERT INTO ld (user, writer, mess, time, reason, type, srok) values('".addslashes($u)."', '".addslashes($w)."', '".addslashes($m)."', '".time()."', '".addslashes($r)."', '".addslashes($t)."', '".addslashes($s)."')");
	}

    public function indexAction()
    {
		$message = '';

		$mode = $this->request->get('mode');

		if ($mode == "add" && ($this->user->b_tribe > 0 || $this->user->tribe_rank == 5))
		{
			$login = $this->request->getPost('login');

			if (empty($login))
				$message = "Укажите логин персонажа, которого Вы хотите принять в клан!";
			else
			{
				$hinfo = $this->db->query("SELECT username, id, room, rank, level, tribe, ic FROM game_users WHERE username = '" . addslashes($login) . "' LIMIT 1")->fetch();

				$members = $this->db->query("SELECT SUM(*) AS num FROM game_users WHERE tribe = '" . $this->user->tribe . "'")->fetch()['num'];
				$price = $members * 200;

				if (!isset($hinfo['id']))
					$message = "Персонаж <u>" . $login . "</u> не найден!";
				elseif ($hinfo['id'] == $this->user->id)
					$message = "Вы и так состоите в клане <U>" . $this->tribe['name'] . "</U>!";
				elseif ($hinfo['tribe'] > 0)
					$message = "Персонаж <U>" . $hinfo['user'] . "</U> состоит в другом клане.";
				elseif ($hinfo['rank'] > 99)
					$message = "Вы не можете принимать в клан должностных лиц!";
				elseif ($hinfo['level'] < 4)
					$message = "Вступать в клан могут персонажи не ниже 4 уровня!";
				elseif ($this->tribe['kazna'] < $price)
					$message = "В казне клана нету " . $price . " кредитов!";
				elseif ($hinfo['ic'] < time())
					$message = "Персонаж либо слишком давно проходил проверку у Инквизиторов, либо не проходил её вовсе!";
				else
				{
					$success = $this->db->query("UPDATE game_users SET tribe = '" . $this->tribe['id'] . "', b_tribe = 0, tribe_rank = '' WHERE id = '" . $hinfo['id'] . "'");

					if ($success)
					{
						$this->db->query("UPDATE game_tribes SET kazna = kazna - '" . $price . "' WHERE id = '" . $this->tribe['id'] . "'");
						$this->tribe['kazna'] = $this->tribe['kazna'] - $price;

						$this->ld_m(4, $hinfo['username'], 'Администратор', '', "Принят в клан <U>" . $this->tribe['name'] . "</U> персонажем <U>" . $this->user->username . "</U>", '');

						$this->game->insertInChat("Персонаж <b><u>" . $this->user->username . "</u></b> принял Вас в клан <b><u>" . $this->tribe['name'] . "</u></b>", $hinfo['username']);

						$message = "Вы приняли в клан персонажа <U>" . $hinfo['username'] . "</U> за <U>" . $price . "</U> зол.";
					}
				}
			}
		}

		// Исключение из клана
		if ($mode == "drop" && ($this->user->b_tribe > 0 || $this->user->tribe_rank == 5))
		{
			$login = $this->request->getPost('login');

			if (empty($login) || $login == "Логин")
				$message = "Укажите логин персонажа, которого Вы хотите принять в клан!";
			else
			{
				$hinfo = $this->db->query("SELECT username, id, room, rank, level, tribe, ic FROM game_users WHERE username = '" . addslashes($login) . "' LIMIT 1")->fetch();

				if (!isset($hinfo['id']))
					$message = "Персонаж <u>" . $login . "</u> не найден!";
				elseif ($hinfo['id'] == $this->user->id)
					$message = "Вы не можете исключить из клана самого себя!";
				elseif ($hinfo['tribe'] != $this->tribe['id'])
					$message = "Персонаж <U>" . $hinfo['user'] . "</U> не состоит в вашем клане!";
				elseif ($hinfo['ic'] < time())
					$message = "Персонаж либо слишком давно проходил проверку у Инквизиторов, либо не проходил её вовсе!";
				elseif ($hinfo['b_tribe'] == 1)
					$message = "Персонаж <U>" . $hinfo['user'] . "</U> является главой клана " . $this->tribe['name'] . ".<BR>Никто не в праве исключить главу клана!";
				else
				{
					$success = $this->db->query("UPDATE game_users SET tribe = 0, b_tribe = 0, tribe_rank = '' WHERE id = '" . $hinfo['id'] . "'");

					if ($success)
					{
						$this->ld_m(4, $hinfo['username'], 'Администратор', '', "Исключён из клана <U>" . $this->tribe['name'] . "</U> персонажем <U>" . $this->user->username . "</U>", '');

						$this->game->insertInChat("Персонаж <b><u>" . $this->user->username . "</u></b> исключил Вас из клана <b><u>" . $this->tribe['name'] . "</u></b>", $hinfo['username']);

						$message = "Вы исключили из клана персонажа <U>" . $hinfo['username'] . "</U>.";
					}
				}
			}
		}

		// Сложение полномочий
		if ($mode == "tcp" && $this->user->b_tribe == 1)
		{
			$login = $this->request->getPost('login');

			if (empty($login) || $login == "Логин")
				$message = "Укажите логин персонажа, на которого Вы хотите сложить полномочия!";
			else
			{
				$hinfo = $this->db->query("SELECT username, id, room, rank, level, tribe, ic FROM game_users WHERE username = '" . addslashes($login) . "' LIMIT 1")->fetch();

				if (!isset($hinfo['id']))
					$message = "Персонаж <u>" . $login . "</u> не найден!";
				elseif ($this->user->b_tribe != 1)
					$message = "У Вас нет полномочий, что бы их складывать! :)";
				elseif ($hinfo['id'] == $this->user->id)
					$message = "Зачем складывать полномочия на самого себя? :)";
				elseif ($hinfo['tribe'] != $this->user->tribe)
					$message = "Персонаж <U>" . $hinfo['username'] . "</U> не состоит в Вашем клане!";
				else
				{
					$success = $this->db->query("UPDATE game_users t1, game_users t2 SET t1.b_tribe = 0, t2.b_tribe = 1, t2.tribe_rank = '' WHERE t1.id = '" . $this->user->id . "' AND t2.id = '" . $hinfo['id'] . "'");

					if ($success)
					{
						$this->user->b_tribe = 0;

						$this->game->insertInChat("Персонаж <b><u>" . $this->user->username . "</u></b> сложил на Вас полномочия главы клана <b><u>" . $this->tribe['name'] . "</u></b>", $hinfo['username']);

						$message = "Вы сложили полномочия на персонажа <U>" . $hinfo['username'] . "</U>.";
					}
				}
			}
		}

		// Редактирование статуса
		if ($mode == "edit" && ($this->user->b_tribe > 0 || $this->user->tribe_rank == 5))
		{
			$login = $this->request->getPost('login');

			if (empty($login) || $login == "Логин")
				$message = "Укажите логин персонажа, статус которого вы хотите изменить!";
			else
			{
				$hinfo = $this->db->query("SELECT username, id, room, rank, level, tribe, ic FROM game_users WHERE username = '" . addslashes($login) . "' LIMIT 1")->fetch();

				if (!isset($hinfo['id']))
					$message = "Персонаж <u>" . $login . "</u> не найден!";
				elseif ($hinfo['tribe'] != $this->user->tribe)
					$message = "Персонаж <U>" . $hinfo['username'] . "</U> не состоит в вашем клане!";
				elseif ($this->user->b_tribe != 1 && $this->user->tribe_rank != 5)
					$message = "Нет доступа к данной функции!";
				elseif ($hinfo['b_tribe'] == 1)
					$message = "Персонаж <U>" . $hinfo['username'] . "</U> является главой клана " . $this->user->tribe . ".<BR>Никто не в праве изменить статус главы клана!";
				else
				{
					$this->db->query("UPDATE game_users SET tribe_rank = '" . addslashes($_POST['addstatus']) . "' WHERE id = '" . $hinfo['id'] . "'");

					$message = "Статус персонажа <U>" . $hinfo['username'] . "</U> успешно изменён!";
				}
			}
		}

		// казна
		if ($mode == "addmoney")
		{
			if (empty($money) || $money == "Сумма")
				$message = "Укажите сумму, которую желаете перечислить на клановый счёт!";
			else
			{
				$money = str_replace(',', '.', $money);

				if (!is_float($money))
					$message = "Некорректно задано число!";
				elseif ($this->user->credits < $money)
					$message = "Недостаточно кредитов!";
				else
				{
					$this->db->query("INSERT INTO tribe_log (time, user_id, tribe_id, action) values('".time()."', '".$this->user->id."','".$this->user->tribe."', 'Перевел в казну $money зол.')");
					$this->db->query("UPDATE game_users SET credits = credits - '" . floatval($money) . "' WHERE id = '" . $this->user->id . "'");
					$this->db->query("UPDATE game_tribes SET kazna = kazna + '" . floatval($money) . "' WHERE id = '" . $this->user->tribe . "'");

					$this->tribe['kazna'] += $money;

					$message = "Вы успешно перечислили сумму <U>" . $money . "</U> зол. в казну клана!";
				}
			}
		}

		if ($mode == "givemoney")
		{
			if (empty($money) || $money == "Сумма")
				$message = "Укажите сумму, которую желаете снять с  кланового счёта!";
			else
			{
				$money = str_replace(',', '.', $money);

				if (!is_float($money))
					$message = "Некорректно задано число!";
				elseif ($this->tribe['kazna'] < $money)
					$message = "Недостаточно кредитов!";
				elseif ($this->user->b_tribe == 0 && $this->user->tribe_rank > 3)
					$message = "Нет доступа к данной функции!";
				else
				{
					$this->db->query("INSERT INTO tribe_log (time, user_id, tribe_id, action) values('".time()."', '".$this->user->id."','".$this->user->tribe."', 'Взял из казны $money зол.')");
					$this->db->query("UPDATE game_users SET credits = credits + '" . floatval($money) . "' WHERE id = '" . $this->user->id . "'");
					$this->db->query("UPDATE game_tribes SET kazna=kazna-'" . floatval($money) . "' WHERE id = '" . $this->user->tribe . "'");

					$this->tribe['kazna'] -= $money;

					$message = "Вы успешно сняли сумму <U>" . $money . "</U> зол. с казны клана!";
				}
			}
		}

		$this->view->setVar('message', $message);
	}
}
?>