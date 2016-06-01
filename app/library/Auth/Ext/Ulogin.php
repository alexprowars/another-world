<?php

namespace App\Auth\Ext;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\User\Component;

/**
 * Class Ulogin
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Assets\Manager assets
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Session\Adapter\Memcache session
 * @property \Phalcon\Http\Response\Cookies cookies
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Config config
 */
class Ulogin extends Component implements ExtAuthInterface
{
	private $token = '';
	private $data = array();
	private $isLogin = false;

	function __construct($token)
	{
		$s = file_get_contents('http://u-login.com/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
		$this->data = json_decode($s, true);

		$this->token = $token;

		if (isset($this->data['identity']))
		{
			$this->isLogin = true;
			$this->login();
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

		$Row = $this->db->query("SELECT u.id, ui.password, a.id AS auth_id FROM game_users u, game_users_info ui, game_users_auth a WHERE ui.id = u.id AND a.user_id = u.id AND a.external_id = '".$this->data['identity']."';")->fetch();

		if (!isset($Row['id']))
			$this->register();
		else
		{
			$this->db->updateAsDict(
				"game_users_auth",
				['enter_time' => time()],
				"id = ".$Row['auth_id']
			);

			$this->auth->auth($Row['id'], $Row['password'], 0, (time() + 2419200));
		}

		$this->response->redirect('game/');
		$this->response->send();

		die();
	}

	public function register ()
	{
		$check = $this->db->query("SELECT user_id FROM game_users_auth WHERE external_id = '".$this->data['identity']."'")->fetch();

		if (isset($check['user_id']))
		{
			$find = $this->db->query("SELECT id FROM game_users WHERE id = ".$check['user_id']."")->fetch();

			if (!isset($find['id']))
				$this->db->query("DELETE FROM game_users_auth WHERE user_id = ".$check['user_id']."");
			else
				return false;
		}

		$refer = (isset($_SESSION['ref']) ? intval($_SESSION['ref']) : 0);

		if ($refer != 0)
		{
			$refe = $this->db->query("SELECT id FROM game_users_info WHERE id = '".$refer."'")->fetch();

			if (!isset($refe['id']))
				$refer = 0;
		}

		$this->db->query("LOCK TABLES game_users_info WRITE, game_users WRITE, game_users_auth WRITE");

		$this->db->insertAsDict(
			"game_users",
			array
			(
				'username'		=> trim($this->data['first_name']." ".$this->data['last_name']),
				'sex'			=> ($this->data['sex'] > 0 ? ($this->data['sex'] == 1 ? 2 : 1) : 1),
				'ip'			=> sprintf("%u", ip2long($this->request->getClientAddress())),
				'bonus'			=> time(),
				'onlinetime'	=> time()
			)
		);

		$iduser = $this->db->lastInsertId();

		if ($iduser > 0)
		{
			$this->db->insertAsDict(
				"game_users_info",
				array
				(
					'id'			=> $iduser,
					'email'			=> '',
					'register_time'	=> time(),
					'password'		=> md5($this->token)
				)
			);

			$this->db->insertAsDict(
				"game_users_auth",
				array
				(
					'user_id'			=> $iduser,
					'external_id'		=> $this->data['identity'],
					'register_time'		=> time(),
					'enter_time'		=> time()
				)
			);

			$this->db->query("UNLOCK TABLES");

			$this->db->insertAsDict(
				"game_slots",
				['user_id' => $iduser]
			);

			if ($refer != 0)
			{
				$ref = $this->db->query("SELECT id FROM game_users_info WHERE id = '".$refer."'")->fetch();

				if (isset($ref['id']))
				{
					$this->db->insertAsDict(
						"game_refs",
						array
						(
							'r_id'	=> $iduser,
							'u_id'	=> $refer
						)
					);
				}
			}

			$this->db->query("UPDATE game_config SET `value` = `value` + 1 WHERE `key` = 'totalUsers'");
			$this->config->app->totalUsers++;

			$this->auth->auth($iduser, md5($this->token));

			return true;
		}
		else
		{
			$this->db->query("UNLOCK TABLES");

			return false;
		}
	}
}