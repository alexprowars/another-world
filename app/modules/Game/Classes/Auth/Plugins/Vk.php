<?php

namespace Game\Auth\Plugins;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Game\Models\User;
use Phalcon\Mvc\User\Component;
use Phalcon\Text;
use Sky\Core\Options;

class Vk extends Component implements AuthInterface
{
	private $isLogin = false;
	private $data = array();

	public function check ()
	{
		if ($this->request->hasPost('viewer_id') && $this->request->hasPost('auth_key'))
		{
			if (md5($this->config->vk->id."_".$this->request->getPost('viewer_id', 'int')."_".$this->config->vk->secret) == $this->request->getPost('auth_key'))
			{
				$uInfo = $this->send('users.get', array('user_ids' => $_POST['viewer_id'], 'fields' => 'sex'));

				$this->data = $uInfo['response'][0]['user'];

				if (count($uInfo['response']))
				{
					$this->isLogin = true;
					$this->login();
				}
				else
					die('<script type="text/javascript">alert("Параметры авторизации являются некорректными! #1")</script>');
			}
			else
				die('<script type="text/javascript">alert("Параметры авторизации являются некорректными! #2")</script>');
		}
	}

	public function isAuthorized ()
	{
		return $this->isLogin;
	}

	public function login()
	{
		if (!$this->isAuthorized())
			return false;

		$Row = $this->db->query("SELECT u.id, u.tutorial, ui.password, a.id AS auth_id FROM game_users u, game_users_info ui, game_users_auth a WHERE ui.id = u.id AND a.user_id = u.id AND a.external_id = 'http://vk.com/id".$this->request->getPost('viewer_id', 'int')."';")->fetch();

		if (!isset($Row['id']))
			$this->register();
		else
		{
			$this->db->updateAsDict(
				"game_users_auth",
				['enter_time' => time()],
				"id = ".$Row['auth_id']
			);

			$this->auth->authorize($Row['id'], (time() + 2419200));
		}

		echo '<center>Загрузка...</center><script>parent.location.href="/game/?'.http_build_query($_POST).'";</script>';
		die();
	}

	public function register ()
	{
		$uid = $this->request->getPost('viewer_id', 'int', 0);

		if (!$uid)
			return false;

		if ($this->request->hasPost('user_id') && $this->request->hasPost('group_id') && $this->request->hasPost('viewer_type') && ($this->request->getPost('user_id', 'int') != 0 && $this->request->getPost('group_id', 'int') == 0 && $this->request->getPost('viewer_type', 'int') == 1))
			$refer = $this->request->getPost('user_id', 'int');
		else
			$refer = 0;

		$NewPass = Text::random(Text::RANDOM_ALNUM, 9);

		if ($refer != 0)
		{
			$refe = $this->db->query("SELECT id FROM game_users_info WHERE id = '".$refer."'")->fetch();

			if (!isset($refe['id']))
				$refer = 0;
		}

		$check = $this->db->query("SELECT user_id FROM game_users_auth WHERE external_id = 'http://vk.com/id".$uid."'")->fetch();

		if (isset($check['user_id']))
		{
			$find = $this->db->query("SELECT id FROM game_users WHERE id = ".$check['user_id']."")->fetch();

			if (!isset($find['id']))
			{
				$this->db->query("DELETE FROM game_users_auth WHERE user_id = ".$check['user_id']."");
			}
			else
				return false;
		}

		$this->db->query("LOCK TABLES game_users_info WRITE, game_users WRITE, game_users_auth WRITE");

		$user = new User([
			'username'		=> addslashes(str_replace('\'', '', $this->data['first_name'].' '.$this->data['last_name'])),
			'sex'			=> ($this->data['sex'] > 0 ? ($this->data['sex'] == 1 ? 2 : 1) : 1),
			'ip'			=> sprintf("%u", ip2long($this->request->getClientAddress())),
			'bonus'			=> time(),
			'onlinetime'	=> time()
		]);

		if ($user->create())
		{
			$this->db->updateAsDict(
				"game_users_info",
				['password' => md5($NewPass)],
				"id = ".$user->id
			);

			$this->db->insertAsDict(
				"game_users_auth",
				array
				(
					'user_id'			=> $user->id,
					'external_id'		=> 'http://vk.com/id'.$uid,
					'register_time'		=> time(),
					'enter_time'		=> time()
				)
			);

			$this->db->query("UNLOCK TABLES");

			if ($refer != 0)
			{
				$ref = $this->db->query("SELECT id FROM game_users_info WHERE id = '".$refer."'")->fetch();

				if (isset($ref['id']))
				{
					$this->db->insertAsDict(
						"game_refs",
						array
						(
							'r_id' 	=> $user->id,
							'u_id'	=> $refer
						)
					);
				}
			}

			Options::set('users_total', intval(Options::get('users_total', 0, true)) + 1);

			$this->auth->authorize($user->id);

			return true;
		}
		else
		{
			$this->db->query("UNLOCK TABLES");

			return false;
		}
	}

	public function send ($method, $params = array())
	{
		$params['api_id']		= $this->config->vk->id;
		$params['method']		= $method;
		$params['timestamp']	= time() + 100;
		$params['format']		= 'json';
		$params['random']		= rand(0,10000);

		ksort($params);

		$sig = '';

		foreach($params as $k => $v)
			$sig .= trim($k).'='.trim($v);

		$params['sig'] = md5($sig.$this->config->vk->secret);

		return json_decode(file_get_contents($this->config->vk->api.'?'.http_build_query($params, null, '&')), true);
	}
}