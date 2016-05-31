<?php
namespace App\Game\Controllers;

use Phalcon\Mvc\View;

class PersController extends Application
{
	public function initialize ()
	{
		parent::initialize();
	}

	public function indexAction()
	{
		$this->tag->prependTitle('Персонаж');
	}

	public function configAction ()
	{
		// Изменение конфига игрока
		if ($this->request->hasPost('mystat'))
		{
			if (is_numeric($this->request->getPost('update_status')))
			{
				$this->user->status = $this->request->getPost('update_status', 'int');

				$this->db->query("UPDATE game_users SET status = '" . $this->user->status . "' WHERE id = '" . $this->user->getId() . "'");

				$this->cookies->set('chat_showonline', $this->request->getPost('showonline', 'int'));
				$this->cookies->set('chat_showstat', $this->request->getPost('showstat', 'int'));
				$this->cookies->send();
			}
		}

		if ($this->request->hasPost('mychat'))
		{
			$this->cookies->set('translit', $this->request->getPost('translit'));
			$this->cookies->set('sysmsg', $this->request->getPost('sysmsg'));
			$this->cookies->set('sysmsg1', $this->request->getPost('sysmsg1'));
			$this->cookies->send();
		}
	}

	public function updatesAction ()
	{
		$msg = '';

		$update = $this->request->getQuery('update', null, '');

		// ----- # Повышаем физический параметр # ----- //
		if (!empty($update))
		{
			if ($this->user->s_updates > 0)
			{
				$st_name = '';

				switch ($update)
				{
					case 'strength':
						$st_name = "strength";
						break;
					case 'dex':
						$st_name = "dex";
						break;
					case 'agility':
						$st_name = "agility";
						break;
					case 'vitality':
						$st_name = "vitality";
						break;
					case 'power':
						$st_name = "power";
						break;
					case 'razum':
						$st_name = "razum";
						break;
					case 'battery':
						$st_name = "battery";
						break;
					case 'duh':
						$st_name = "duh";
						break;
				}

				if ($st_name != '')
				{
					$this->user->s_updates--;
					$this->user->{$st_name}++;

					$this->db->query("UPDATE game_users SET s_updates = s_updates - 1, " . $st_name . " = " . $st_name . " + 1 WHERE id = " . $this->user->getId() . " AND s_updates > 0");

					$msg = "Удачно увеличили физический параметр \""._getText('stats', $st_name)."\"!";
				}
			}
			else
				$msg = "У Вас нет свободных увеличений!";
		}
		###

		$this->view->setVar('msg', $msg);
	}

	public function friendsAction ()
	{
		$this->view->disableLevel(View::LEVEL_LAYOUT);

		$message = '';

		if ($this->request->getQuery('act', null, '') == "add")
		{
			$username = addslashes(htmlspecialchars($this->request->getPost('name')));

			if (!preg_match("/^[a-zA-Za-яA-Я0-9_\.\,\-\!\?\*\ ]+$/u", $username))
				$message = "Ник имеет запрещенные символы";
			else
			{
				$friend = $this->db->query("SELECT `id` FROM `game_users` WHERE `username` = '".$username."'")->fetch();

				if (!isset($friend['id']))
					$message = "Персонаж не существует";
				elseif ($friend['id'] == $this->user->id)
					$message = "Вы неможете добавить себя в свой список";
				elseif (!is_numeric($_POST['dr']) || $_POST['dr'] > '1')
					$message = "Ну ты и читерюга!!!";
				else
				{
					$check = $this->db->query("SELECT `id` FROM game_friends WHERE user_id = '" . $this->user->id . "' AND friend_id = '" . $friend['id'] . "'");

					if ($check->numRows() > 0)
						$message = "Персонаж уже записан в ваш список";
					else
					{
						$this->db->query("INSERT INTO `game_friends` (user_id, friend_id, ignor) VALUES ('" . $this->user->id . "','" . $friend['id'] . "','" . intval($_POST['dr']) . "')");

						$message = "Персонаж добавлен в ваш список";
					}
				}
			}
		}
		elseif ($this->request->getQuery('act', null, '') == "del")
		{
			$username = addslashes(htmlspecialchars($this->request->getPost('name')));

			if (!preg_match("/^[a-zA-Za-яA-Я0-9_\.\,\-\!\?\*\ ]+$/u", $username))
				$message = "Ник имеет запрещенные символы";
			else
			{
				$friend = $this->db->query("SELECT `id` FROM `game_users` WHERE `username` = '".$username."'")->fetch();

				if (!isset($friend['id']))
					$message = "Персонаж не существует";
				else
				{
					$check = $this->db->query("SELECT * FROM game_friends WHERE user_id = '" . $this->user->id . "' AND friend_id = '".$friend['id']."'");

					if ($check->numRows() > 0)
					{
						$this->db->query("DELETE FROM game_friends WHERE user_id = '" . $this->user->id . "' AND friend_id = '".$friend['id']."'");

						$message = "Персонаж " . $username . " удален из вашего списка";
					}
					else
						$message = "Персонаж " . $username . " не удален из вашего списка";
				}
			}
		}

		$this->view->setVar('message', $message);

		$list = $this->db->query("SELECT `f`.*, u.username, u.tribe, `u`.level, `u`.rank, `u`.room, `u`.onlinetime FROM `game_friends` f, `game_users` u WHERE `f`.`user_id` = '".$this->user->id."' AND `u`.`id` = `f`.`friend_id`")->fetchAll();

		$this->view->setVar('list', $list);
	}

	public function anketaAction ()
	{
		$message = '';

		$info = $this->db->query("SELECT * FROM game_users_info WHERE id = '".$this->user->id."'")->fetch();

		if ($this->request->hasPost('changepass'))
		{
			if ($this->request->getPost('old_pass') != '')
			{
				if (md5($this->request->getPost('old_pass')) == $this->user->pass)
				{
					if ($this->request->getPost('new_pass') == $this->request->getPost('conf_new_pass'))
					{
						if (strlen($this->request->getPost('new_pass')) >= 6)
						{
							$pass = md5($this->request->getPost('new_pass'));
							$this->db->query("UPDATE game_users_info SET password = '" . $pass . "' WHERE id = '" . $this->user->id . "'");

							$message = "Ваш пароль успешно изменён!";
						}
						else
							$message = "Пароль не должен быть короче 6 символов!";
					}
					else
						$message = "Введённые пароли не совпадают! Будте аккуратны!";
				}
				else
					$message = "Вы ошиблись при написании пароля! Будте аккуратны!";
			}
			else
				$message = "Введите старый пароль!";
		}

		if ($this->request->hasPost('changemail'))
		{
			if ($this->request->getPost('old_email') != '')
			{
				if ($this->request->getPost('old_email') == $info['email'])
				{
					if (preg_match("/^[_\.0-9a-zA-Z-]{1,}@[_\.0-9a-zA-Z-]{1,}\.[_\.0-9a-zA-Z-]{2,}$/", $this->request->getPost('new_email')))
					{
						$this->db->query("UPDATE game_users_info SET email = '" . $this->request->getPost('new_email') . "' WHERE id = '".$this->user->id."'");

						$message = "Ваш e-mail успешно изменён!";
					}
					else
						$message = "Адрес электронной почты содержит запрещённые символы!";
				}
				else
					$message = "Вы ошиблись при написании e-mail! Будте аккуратны!";
			}
			else
				$message = "Введите старый email!";
		}

		if ($this->request->hasPost('changeinfo'))
		{
			$realname 	= addslashes(htmlspecialchars($this->request->getPost('realname', null, '')));
			$city 		= addslashes(htmlspecialchars($this->request->getPost('city', null, '')));
			$about 		= addslashes(htmlspecialchars($this->request->getPost('about', null, '')));

			$this->db->query("UPDATE game_users u, game_users_info ui SET ui.name = '" . $realname . "', ui.city = '" . $city . "', ui.about = '" . $about . "' WHERE u.id = '".$this->user->id."' and ui.id = '".$this->user->id."'");

			$info['name'] 	= $realname;
			$info['city'] 	= $city;
			$info['about'] 	= $about;
		};

		$this->view->setVar('message', $message);
		$this->view->setVar('anketa', $info);
	}

	public function priemAction ()
	{
		$message = '';

		/**
		 * @var array $priem_full
		 */
		include(APP_PATH.'app/library/Battle/vars.php');

		$onset_priem = $this->request->getQuery('onset_priem', 'int', 0);
		$unset_priem = $this->request->getQuery('unset_priem', 'int', 0);

		$active = $this->db->query("SELECT * FROM game_users_priems WHERE user_id = ".$this->user->getId()."")->fetch();

		if (!isset($active['id']))
			$this->db->query("INSERT INTO game_users_priems SET user_id = ".$this->user->getId()."");

		if ($onset_priem > 0)
		{
			if (!$priem_full[$onset_priem])
				$message = "Такого приёма не существует";
			if ($this->user->level < $priem_full[$onset_priem]['level'])
				$message="Уровень слишком мал!";
			else
			{
				$slot = 1;

				for ($i = 1; $i <= 10; $i++)
				{
					if (!$active[$i])
					{
						$slot = $i;
						break;
					}
				}

				$active[$slot] = $onset_priem;

				$this->db->query("UPDATE game_users_priems SET `".$slot."` = '".$onset_priem."' WHERE user_id = ".$this->user->getId()."");

			}
		}

		if ($unset_priem > 0)
		{
			if ($unset_priem > 10)
				$message = "Неправильный ввод данных";
			else
			{
				$this->db->query("UPDATE game_users_priems SET `".$unset_priem."` = '0' WHERE user_id = ".$this->user->getId()."");
				$active[$unset_priem] = 0;
			}
		}

		foreach ($priem_full as $k => $v)
		{
			if (in_array($k, $active))
				$priem_full[$k]['onset'] = 'Y';
		}

		$this->view->setVar('message', $message);
		$this->view->setVar('priems', $active);
		$this->view->setVar('priem_full', $priem_full);
	}

	public function workAction ()
	{
		$this->view->disableLevel(View::LEVEL_LAYOUT);

		$refers = $this->db->query("SELECT id, username, level, onlinetime FROM game_users WHERE refer = '" . $this->user->id . "'")->fetchAll();

		$this->view->setVar('refers', $refers);
	}
}
 
?>