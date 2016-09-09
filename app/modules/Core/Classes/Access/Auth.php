<?php

namespace Sky\Core\Access;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Sky\Core\Models\Session;
use Game\Models\User;
use Phalcon\Mvc\User\Component;

/**
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Assets\Manager assets
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Session\Adapter\Memcache session
 * @property \Phalcon\Http\Response\Cookies cookies
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Mvc\Router router
 * @property \Phalcon\Config|\stdClass config
 */
class Auth extends Component
{
	private $_authorized = false;
	private $_plugins = [];

	public function isAuthorized()
	{
		return $this->_authorized;
	}

	public function addPlugin ($className)
	{
		$this->_plugins[] = $className;
	}

	function check ()
	{
		$result = $this->checkUserAuth();

		if ($this->_authorized && $result)
			return $result;

		return false;
	}

	public function getSessionKey ()
	{
		return "session_id";
	}

	private function checkUserAuth ()
	{
		$this->eventsManager->fire('core:beforeAuthCheck', $this);

		foreach ($this->_plugins as $plugin)
		{
			$ext = new $plugin();
			/** @noinspection PhpUndefinedMethodInspection */
			$ext->check();
		}

		if (!$this->cookies->has($this->getSessionKey()))
			return false;

		$sessionId = $this->cookies->get($this->getSessionKey())->getValue();

		$session = Session::findFirst(["conditions" => "token = ?0", "bind" => [$sessionId]]);

		if (!$session || $session->object_type != Session::OBJECT_TYPE_USER)
		{
			$this->remove();

			return false;
		}

		$user = User::findFirst($session->object_id);

		if (!$user)
			$this->remove();
		else
			$this->_authorized = true;

		$this->eventsManager->fire('core:afterAuthCheck', $this, $user);

		return $user;
	}

	public function authorize ($userId, $expire = 0)
	{
		$session = Session::start(Session::OBJECT_TYPE_USER, $userId, $expire);

		if (!$session)
			throw new \Exception("Can`t start auth session");

		$this->cookies->set($this->getSessionKey(), $session->token, $expire, '/', 0, $_SERVER["SERVER_NAME"]);
		$this->cookies->send();

		if ($this->session->isStarted())
		{
			$this->session->destroy(true);
			$this->session->start();
		}
	}

	public function remove ($redirect = false)
	{
		$this->session->destroy(true);

		$sessionCookie = $this->cookies->get($this->getSessionKey());
		$sessionCookie->setPath('/');
		$sessionCookie->setDomain($_SERVER["SERVER_NAME"]);

		$sessionId = $sessionCookie->getValue();

		$session = Session::findFirst(["conditions" => "token = ?0", "bind" => [$sessionId]]);

		if ($session)
			$session->delete();

		$sessionCookie->delete();

		$this->_authorized = false;

		if ($redirect)
		{
			$this->response->redirect('')->send();
			die();
		}
	}
}