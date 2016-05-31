<?php
namespace App\Auth;

use App\Auth\Ext\Ulogin;
use App\Auth\Ext\Vk;
use App\Auth\Ext\Ok;
use App\Models\Users;
use Phalcon\Db;
use Phalcon\Mvc\User\Component;

/**
 * Class Auth
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Assets\Manager assets
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Session\Adapter\Memcache session
 * @property \Phalcon\Http\Response\Cookies cookies
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Mvc\Router router
 */
class Auth extends Component
{
	private $IsUserChecked = false;

	public function checkExtAuth ()
	{
		// Авторизация через ulogin
		if ($this->request->has('token') && $this->request->get('token') != '' && $this->router->getControllerName() != 'options')
		{
			new Ulogin($this->request->get('token'));
		}

		if ($this->request->has('authId') && $this->request->has('authSecret'))
		{
			$this->cookies->set($this->config->cookie->prefix.'_id', 		$this->request->get('authId', 'int'));
			$this->cookies->set($this->config->cookie->prefix.'_secret', 	$this->request->get('authSecret'));
		}
	}

	public function isAuthorized()
	{
		return $this->IsUserChecked;
	}

	public function getSecret ($uid, $password, $security = 0)
	{
		return ($security) ? md5($password."---".$this->request->getClientAddress()."---IPSECURITYFLAG_Y---".$uid) : md5($password."---IPSECURITYFLAG_N---".$uid);
	}

	function check ()
	{
		$Result = $this->checkUserAuth();

		if ($this->IsUserChecked)
		{
			if ($this->request->has('set') || $this->router->getControllerName() != 'banned')
			{
				if ($Result->banaday > time())
					die('Ваш аккаунт заблокирован. Срок окончания блокировки: '.datezone("d.m.Y H:i:s", $Result->banaday).'<br>Для получения дополнительной информации зайдите <a href="/banned/">сюда</a>');
				elseif ($Result->banaday > 0 && $Result->banaday < time())
				{
					$this->db->query("DELETE FROM game_banned WHERE `who` = '".$Result->id."'");
					$this->db->query("UPDATE game_users SET`banaday` = '0' WHERE `id` = '".$Result->id."'");

					$Result->banaday = 0;
				}
			}

			return $Result;
		}

		return false;
	}

	private function checkUserAuth ()
	{
		$UserRow = array();

		if (!$this->session->has('uid') && $this->cookies->has($this->config->cookie->prefix.'_id') && $this->cookies->has($this->config->cookie->prefix.'_secret'))
		{
			$UserResult = $this->db->query("SELECT u.*, ui.password FROM game_users u, game_users_info ui WHERE ui.id = u.id AND u.`id` = '".$this->cookies->get($this->config->cookie->prefix.'_id')->getValue('int')."'");

			if ($UserResult->numRows() == 0)
				$this->remove();
			else
			{
				$raw = $UserResult->fetch();

				$UserRow = new Users;
				$UserRow->assign($raw);

				$options = $UserRow->unpackOptions($UserRow->options_toggle);

				if ($this->getSecret($UserRow->id, $raw['password'], $options['security']) != $this->cookies->get($this->config->cookie->prefix.'_secret')->getValue())
					$this->remove();
				else
				{
					$this->session->set('uid', $UserRow->id);
					$this->session->set('unm', $UserRow->username);

					$this->IsUserChecked = true;
				}
			}
		}
		elseif ($this->session->has('uid'))
		{
			if (!$this->cookies->has($this->config->cookie->prefix.'_id') || !$this->cookies->has($this->config->cookie->prefix.'_secret'))
				$this->remove();
			else
			{
				$UserRow = Users::findFirst($this->session->get('uid'));

				if ($UserRow->id <= 0)
					$this->remove();
				else
					$this->IsUserChecked = true;
			}
		}

		if ($this->IsUserChecked)
		{
			if ($UserRow->ip != sprintf("%u", ip2long($this->request->getClientAddress())))
			{
				$update = [];
				$update['ip'] = sprintf("%u", ip2long($this->request->getClientAddress()));

				$this->db->insertAsDict(
					"game_log_ip",
					array
					(
						'id'	=> $UserRow->id,
						'time'	=> time(),
						'ip'	=> sprintf("%u", ip2long($this->request->getClientAddress()))
					)
				);

				$this->db->updateAsDict(
				   	"game_users",
					$update,
				   	"id = ".$UserRow->id
				);
			}
		}

		return $UserRow;
	}

	public function auth ($userId, $password, $security = 0, $expire = 0)
	{
		$secret = $this->getSecret($userId, $password, $security);

		$this->cookies->set($this->config->cookie->prefix."_id", 		$userId, $expire);
		$this->cookies->set($this->config->cookie->prefix."_secret", 	$secret, $expire);
		$this->cookies->send();

		if ($this->session->isStarted())
			$this->session->destroy();
	}

	public function remove($redirect = true)
	{
		if ($this->session->isStarted())
			$this->session->destroy();

		$this->cookies->get($this->config->cookie->prefix."_id")->delete();
		$this->cookies->get($this->config->cookie->prefix."_secret")->delete();
		$this->cookies->get($this->config->cookie->prefix."_extid")->delete();
		$this->cookies->send();

		if ($redirect)
			$this->response->redirect('')->send();
	}
}
 
?>