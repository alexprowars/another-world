<?php

namespace Game;

use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Mvc\View;
use Phalcon\Tag;
use Sky\Core\Lang;

/**
 * Class Application
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Assets\Manager assets
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Mvc\Model\Manager modelsManager
 * @property \Phalcon\Session\Adapter\Memcache session
 * @property \Phalcon\Http\Response\Cookies cookies
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Mvc\Router router
 * @property \Phalcon\Cache\Backend\Memcache cache
 * @property \Phalcon\Mvc\Url url
 * @property \App\Models\User user
 * @property \Sky\Core\Access\Auth auth
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \Phalcon\Flash\Direct flash
 * @property \stdClass config
 * @property \Game\Game game
 */
class Controller extends PhalconController
{
	private static $isInitialized = false;

    public function initialize()
	{
		if (self::$isInitialized)
			return true;

		self::$isInitialized = true;

		if ($this->getDI()->has('game'))
			new \Exception('game module not initialized');

		if (function_exists('sys_getloadavg'))
		{
			$load = sys_getloadavg();

			if ($load[0] > 15)
			{
				header('HTTP/1.1 503 Too busy, try again later');
				die('Server too busy. Please try again later.');
			}
		}

		$this->view->setMainView('game');

		Lang::includeLang('main', 'game');

		if ($this->request->isAjax())
			$this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
		else
		{
			$this->tag->setTitleSeparator(' | ');
			$this->tag->setTitle($this->config->app->name);
	        $this->tag->setDocType(Tag::HTML5);

			$this->assets->addJs('js/jquery-1.11.2.min.js');
			$this->assets->addJs('js/jquery-ui.js');
			$this->assets->addJs('js/jquery.form.min.js');
			$this->assets->addJs('js/jquery.toast.min.js');
			$this->assets->addJs('js/jquery.confirm.min.js');
			$this->assets->addJs('js/show_inf.js');
			$this->assets->addJs('js/battle.js');
			$this->assets->addJs('js/shop.js');
			$this->assets->addJs('js/main.js');

			$this->assets->addCss('css/jquery-ui.css');
			$this->assets->addCss('css/jquery.toast.min.css');
			$this->assets->addCss('css/jquery.confirm.min.css');
			$this->assets->addCss('css/bootstrap.css');
			$this->assets->addCss('css/style.css');
		}

		if ($this->auth->isAuthorized())
		{
			$this->user->hp_now = round($this->user->hp_now);

			if (($this->user->r_time > 0 && $this->user->r_type == 0) || ($this->user->r_time == 0 && $this->user->r_type != 0))
			{
				$this->user->r_time = 0;
				$this->user->r_type = 0;
				$this->user->update();
			}

			if ($this->user->last_battle)
			{
				$this->user->last_battle = 0;
				$this->user->update();
			}

			$dispatch = '';

			if ($this->user->battle > 0)
			{
				$dispatch = 'battle';
			}
			elseif ($this->user->r_time > 0 && $this->user->r_type != 0)
			{
				switch ($this->user->r_type)
				{
					case 1:
						$this->checkRoom(666);
						$dispatch = 'map';
						break;
					case 2:
						$this->checkRoom(8);
						$dispatch = 'map';
						break;
					case 3:
						$this->checkRoom(9);
						$dispatch = 'map';
						break;
					case 4:
						$this->checkRoom(16);
						$dispatch = 'map';
						break;
					case 7:
						$this->checkRoom(11);
						$dispatch = 'map';
						break;
					case 10:
						$dispatch = 'map';
						break;
				}
			}
			elseif ($this->user->t_time)
			{
				$this->checkRoom (666);
				$dispatch = 'map';
			}

			if ($this->request->hasPost('usemagic') && $this->request->isAjax() && ($dispatch == '' || $dispatch == 'battle'))
			{
				$this->view->disable();
				$this->game->setRequestMessage($this->game->useMagic($this->request->getPost('useid'), $this->request->getPost('login')));

				return false;
			}

			if ($dispatch != '' && $dispatch != $this->router->getControllerName())
			{
				return $this->response->redirect($this->url->getBasePath().$dispatch.'/');
			}

			if ($this->user->tutorial < 100)
			{
				$tutorial = new Tutorial();
				$this->game->tutorial = $tutorial->getArray();
			}

			$controller = $this->dispatcher->getControllerName();

			if ($controller == 'index')
				$this->dispatcher->forward(['controller' => 'game', 'action' => 'index']);
		}

		return true;
    }
}