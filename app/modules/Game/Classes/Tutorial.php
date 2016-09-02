<?php

namespace Game;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\User\Component;
use Sky\Core\Lang;

/**
 * Class ControllerBase
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\AsSETs\Manager asSETs
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
 */
class Tutorial extends Component
{
	private $data = ['cursor' => '', 'popup' => '', 'toast' => '', 'url' => ''];

	private function init ()
	{
		Lang::includeLang('tutorial');

		$this->switching();
	}

	private function switching ()
	{
		switch ($this->user->tutorial)
		{
			case 0:

				if ($this->router->getControllerName() == 'pers' && $this->router->getActionName() == 'updates')
				{
					$this->nextStage();
					$this->switching();
					break;
				}

				$this->data['popup'] = _getText('tutorial', 0);
				$this->data['url'] = '/pers/updates/';

				break;

			case 1:

				if ($this->user->s_updates == 0)
				{
					$this->nextStage();
					$this->switching();
					break;
				}

				$this->data['toast'] = _getText('tutorial', 1);

				break;

			case 2:

				$this->data['popup'] = _getText('tutorial', 2);
				$this->nextStage();

				break;

			case 3:

				if ($this->router->getControllerName() == 'map' && $this->user->room == 7)
					$this->data['toast'] = _getText('tutorial', 3);

				break;

			case 4:

				// надеть вещь

				break;

			case 5:

				// напасть на бота

				break;

			case 6:

				// саксесс

				break;
		}
	}

	public function nextStage ()
	{
		$this->user->tutorial++;
		$this->db->query("UPDATE `game_users` SET `tutorial` = '".$this->user->tutorial."' WHERE `id` = '" . $this->user->getId() . "'");
	}

	public function getArray ()
	{
		$this->init();

		return $this->data;
	}
}