<?php
namespace App\Controllers;

use App\Lang;
use Phalcon\Mvc\View;

class ChatController extends ControllerBase
{
	public function initialize()
	{
		Lang::setLang($this->config->app->language);
	}

    public function indexAction()
    {
		$this->view->disable();

		$result = array('messages' => []);

		if ($this->request->has('message_id'))
		{
			$addHp = 0;

			if ($this->user->battle == 0 && $this->user->r_type != 2 && ($this->user->hp_now < $this->user->hp_max) && $this->user->hp_max != 0 && $this->user->onlinetime > 0)
			{
				$addHp = round($this->user->hp_max * ((time() - $this->user->onlinetime) / 600), 10);

				if (($this->user->hp_now + $addHp) > $this->user->hp_max)
					$addHp = $this->user->hp_max - $this->user->hp_now;
			}

			$update = ['onlinetime' => time()];

			if ($addHp > 0)
				$update['hp_now'] = $this->user->hp_now + $addHp;

			$this->db->updateAsDict(
			   	"game_users",
				$update,
			   	"id = ".$this->user->getId()
			);

			$room_messages = json_decode($this->cache->get("game_chat"), true);

			$mess_id = $this->request->get('message_id', 'int');
			$last_message_id = $mess_id;

			if (count($room_messages) > 0)
			{
				$color_massive = _getText('colors');

				foreach ($room_messages as $id => $message)
				{
					if ($message[0] <= $mess_id)
						continue;

					$message[5] = nl2br(preg_replace("[\n\r]", "", $message[5]));

					if ($message[6] > 0)
						$message[5] = "<font color=\"" . $color_massive[$message[6]][0] . "\">" . $message[5] . "</font>";

					if (!is_array($message[3]))
						$message[3] = $message[3] == false ? array() : array($message[3]);

					$msg = ['time' => $message[1], 'user' => $message[2], 'to' => $message[3], 'text' => $message[5], 'private' => ($message[4] > 0 ? 1 : 0), 'me' => -1, 'my' => -1];

					if ($message[4] == 0 && count($message[3]) > 0)
					{
						$msg['me'] = in_array($this->user->username, $message[3]) ? 1 : 0;
						$msg['my'] = ($message[2] == $this->user->username) ? 1 : 0;
					}
					elseif ($message[4] > 0 && count($message[3]) > 0 && ($message[2] == $this->user->username || in_array($this->user->username, $message[3])))
					{
						if ($message[2] == '')
							$msg['to'] = [];

						$msg['me'] = $message[2] == $this->user->username ? 0 : 1;
						$msg['my'] = $msg['me'] ? 0 : 1;
					}
					elseif (count($message[3]) == 0)
					{
						$msg['me'] = 0;
						$msg['my'] = $message[2] == $this->user->username ? 1 : 0;
					}

					$last_message_id = $message[0];

					if ($msg['me'] >= 0 && $msg['my'] >= 0)
						$result['messages'][] = $msg;
				}
			}

			$result['last_message'] = (int) $last_message_id;
			$result['hp_now'] = $this->user->hp_now + $addHp;
			$result['hp_add'] = $addHp;

			$this->response->setJsonContent($result);
			$this->response->setContentType('text/json', 'utf8');
			$this->response->send();
			die();
		}
    }

	public function sendAction ()
	{
		$this->view->disable();

		$result = ['success' => 0, 'messages' => []];

		if ($this->request->has('msg') && $this->request->get('msg') != '')
		{
			$message = trim(htmlspecialchars(addslashes($this->request->get('msg'))));

			if ($message != '' && $this->user->silence < time())
			{
				if ($this->session->get('chat_spam', 0) == 0)
					$this->session->set('chat_spam', time() - 5);
				if ($this->session->get('chat_alert', '') === '')
					$this->session->set('chat_alert',0);

				if ($this->session->get('chat_spam', 0) >= time() && $this->user->silence < time())
				{
					$result['messages'][] =
					[
						'time' 		=> time(),
						'user' 		=> 'Коментатор',
						'to' 		=> [],
						'text' 		=> 'Не более 1 сообщения в 5 секунд! Осталось предупреждений: ' . (2 - $this->session->get('chat_alert', 0)) . '',
						'private' 	=> 0,
						'me' 		=> 1,
						'my' 		=> 0
					];

					if ($this->session->get('chat_alert', 0) === 0)
						$this->session->set('chat_alert_time', time());

					$this->session->set('chat_alert', $this->session->get('chat_alert', 0) + 1);

					if ($this->session->get('chat_alert', 0) > 2 && $this->session->get('chat_alert_time', 0) > time() - 60)
					{
						$this->db->query("UPDATE `game_users` SET `silence` = ".time()." + 600 WHERE `id` = '" . $this->user->id . "'");

						$this->user->silence = time() + 600;

						$this->game->insertInChat("<u><b>Комментатор</b></u> запретил общение  персонажу <u><b>" . $this->user->username . "</b></u> за флуд, сроком 10 минут!", "", false);
					}

					$message = '';
				}
				else
				{
					$this->session->set('chat_spam', time() + 5);
					if ($this->session->get('chat_alert_time', 0) < time() - 60)
						$this->session->set('chat_alert', 0);
				}

				if ($message != '')
				{
					$message = str_replace('\\', '', $message);
					$message = str_replace('\\\'', '\'', $message);
					$message = str_replace('\\\\', '\\', $message);
					$message = str_replace('\\&quot;', '&quot;', $message);

					$this->db->insertAsDict(
						"game_log_chat",
						Array
						(
							'user' => $this->user->id,
							'time' => time(),
							'text' => $message
						)
					);

					$lastId =$this->db->lastInsertId();

					$now = time();

					if (preg_match_all("/приватно \[(.*?)\]/u", $message, $private))
					{
						$message = preg_replace("/приватно \[(.*?)\]/u", '', $message);
					}

					if (preg_match_all("/для \[(.*?)\]/u", $message, $to_login))
					{
						$message = preg_replace("/для \[(.*?)\]/u", '', $message);

						if (isset($private['1']) && count($private[1]) > 0)
						{
							$private[1] = array_merge($private[1], $to_login[1]);
							unset($to_login[1]);
						}
					}

					$message = trim($message);
					$message = strtr($message, _getText('stopwords'));

					$username = $this->user->username;

					$config = json_decode($this->session->get('config', '{}'), true);

					if ($this->user->authlevel > 0 && (strpos($message, '/kick') !== false || $message == '/speak') && isset($to_login['1']))
					{
						$check = $this->db->query("SELECT id, authlevel FROM game_users WHERE username = '".$to_login['1']."' LIMIT 1")->fetch();

						if (isset($check['id']) && $check['authlevel'] == 0)
						{
							if ($message == '/speak')
							{
								$this->db->query("UPDATE game_users SET silence = 0 WHERE id = ".$check['id']."");

								$message = 'Модератор '.$this->user->username.' разрешил общение пользователю '.$to_login['1'].'.';
							}
							else
							{
								$time = 15;

								if (strpos($message, '30') !== false)
									$time = 30;
								elseif (strpos($message, '60') !== false)
									$time = 60;
								elseif (strpos($message, '1440') !== false)
									$time = 1440;

								$this->db->query("UPDATE game_users SET silence = ".(time() + $time * 60)." WHERE id = ".$check['id']."");

								$message = 'Модератор '.$this->user->username.' запретил общение пользователю '.$to_login['1'].' на '.$time.' минут.';
							}

							$username = '';
							unset($to_login[1]);
							unset($private[1]);
							$config['color'] = 0;
						}
					}

					$chat = json_decode($this->cache->get("game_chat"), true);

					if (!is_array($chat))
						$chat = array();

					if (count($chat) > 0)
					{
						foreach ($chat as $id => $m)
						{
							if ($m[1] == $now)
								$now++;
						}
					}

					if (!isset($to_login[1]))
						$to_login[1] = array();

					$isPrivate = false;

					if (isset($private['1']) && count($private[1]) > 0)
					{
						$to_login[1] = $private[1];
						$isPrivate = true;
					}

					$to_login[1] = array_unique($to_login[1]);

					$chat = array_reverse($chat);

					foreach ($chat as $i => $mess)
					{
						if ($i >= 25 && $mess[0] < (time() - 120))
							unset($chat[$i]);
					}

					$chat = array_reverse($chat);

					$config['color'] = 0;

					$chat[] = array($lastId, $now, $username, $to_login[1], $isPrivate, $message, ($config['color'] + 0), '');

					$this->cache->save("game_chat", json_encode($chat), 86400);

					$result['success'] = 1;
				}
			}

			if ($this->user->silence > time())
			{
				$result['messages'][] =
				[
					'time' 		=> time(),
					'user' 		=> 'Коментатор',
					'to' 		=> [],
					'text' 		=> 'На вас наложено заклинание молчания. Осталось молчать до: ' . date("d.m.Y H:i", $this->user->silence) . '!',
					'private' 	=> 0,
					'me' 		=> 1,
					'my' 		=> 0
				];
			}
		}

		$this->response->setJsonContent($result);
		$this->response->setContentType('text/json', 'utf8');
		$this->response->send();
		die();
	}

	public function onlineAction ()
	{
		$cookie = [];

		if (!$this->cookies->has($this->config->cookie->prefix."_chat_sort"))
			$cookie['chat_sort'] = 1;
		if (!$this->cookies->has($this->config->cookie->prefix."_chat_show"))
			$cookie['chat_show'] = 1;

		$sort = $this->cookies->get($this->config->cookie->prefix."_chat_sort")->getValue();

		if ($this->request->hasQuery('sort'))
		{
			$sort = $this->request->get('sort', 'int');

			$cookie['chat_sort'] = $sort;
		}

		$show = $this->cookies->get($this->config->cookie->prefix."_chat_show")->getValue();

		if ($this->request->hasQuery('show'))
		{
			$show = $this->request->get('show', 'int');

			$cookie['chat_show'] = $show;
		}

		if (count($cookie))
		{
			foreach ($cookie as $key => $value)
			{
				$this->cookies->set($this->config->cookie->prefix."_".$key, $value);
			}

			$this->cookies->send();
		}

		switch ($sort)
		{
			case 2:
				$sql_order = "username DESC";
				break;
			case 3:
				$sql_order = "level ASC";
				break;
			case 4:
				$sql_order = "level DESC";
				break;
			default:
				$sql_order = "username ASC";
				break;
		}

		switch ($show)
		{
			case 2:
				$sql_show = "AND room = " . $this->user->room;
				break;
			default:
				$sql_show = "";
				break;
		}

		$this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

		$userList = array();

		$users = $this->db->query("SELECT id, username, silence, rank, tribe, level, status, invisible, battle, travma, proff FROM `game_users` WHERE `rank` != '60' AND `onlinetime` > " . (time() - 180) . " " . $sql_show . " ORDER BY " . $sql_order . "");

		while ($pl = $users->fetch())
		{
			if ($pl['invisible'] > time())
			{
				$pl['username'] = "Тень";
				$pl['rank'] = 0;
				$pl['tribe'] = "";
				$pl['level'] = "??";
				$pl['id'] = "1699638901";
				$pl['travma'] = 0;
				$pl['battle'] = 0;
				$pl['silence'] = 0;
				$pl['proff'] = 0;
				$pl['status'] = 0;
			}

			$userList[] = $pl;
		}

		$this->view->setVar('users', $userList);
		$this->view->setVar('sort', $sort);
		$this->view->setVar('show', $show);
	}
}
 
?>