<?php
namespace App\Controllers;

use App\Lang;
use App\Tutorial;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Phalcon\Tag;

/**
 * Class ControllerBase
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
 * @property \App\Models\Users user
 * @property \App\Auth\Auth auth
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \Phalcon\Flash\Direct flash
 * @property \Phalcon\Config config
 * @property \App\Game game
 */
class ControllerBase extends Controller
{
    public function initialize()
	{
		$this->view->setMainView('game');

		Lang::setLang($this->config->app->language);

		if ($this->request->isAjax())
			$this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
		else
		{
			$this->tag->setTitleSeparator(' | ');
			$this->tag->setTitle($this->config->app->name);
	        $this->tag->setDoctype(Tag::HTML5);

			$js = $this->assets->collection('jsHeader');
			$js->addJs('js/jquery-1.11.2.min.js');
			$js->addJs('js/jquery-ui.js');
			$js->addJs('js/jquery.form.min.js');
			$js->addJs('js/jquery.toast.min.js');
			$js->addJs('js/jquery.confirm.min.js');
			$js->addJs('js/show_inf.js');
			$js->addJs('js/battle.js');
			$js->addJs('js/shop.js');
			$js->addJs('js/main.js');
			//$js->setTargetPath('js/final.js');
			//$js->setTargetUri('js/final.js');
			//$js->addFilter(new Jsmin());

			$css = $this->assets->collection('cssHeader');
			$css->addJs('css/jquery-ui.css');
			$css->addJs('css/jquery.toast.min.css');
			$css->addJs('css/jquery.confirm.min.css');
			$css->addJs('css/bootstrap.css');
			$css->addJs('css/main.css');
		}

		if ($this->auth->isAuthorized())
		{
			$this->user->hp_now = round($this->user->hp_now);

			if (($this->user->r_time > 0 && $this->user->r_type == 0) || ($this->user->r_time == 0 && $this->user->r_type != 0))
			{
				$this->db->query("UPDATE `game_users` SET `r_time` = '0', `r_type` = '0' WHERE `id` = '".$this->user->id."'");

				$this->user->r_time = 0;
				$this->user->r_type = 0;
			}

			if ($this->user->last_battle)
				$this->db->query("UPDATE `game_users` SET `last_battle` = '0' WHERE `id` = '" . $this->user->id . "'");

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
				$this->game->setMessage($this->game->useMagic($this->request->getPost('useid'), $this->request->getPost('login')));

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
		}

		return true;
    }

	function checkRoom ($room)
	{
		if ($room != $this->user->room)
		{
			$this->db->query("UPDATE `game_users` SET `room` = '".$room."' WHERE `id` = '".$this->user->id."'");
			$this->user->room = $room;
		}
	}

	public function message ($text, $title = '')
	{
		$this->view->pick('shared/message');
		$this->view->setVar('text', $text);
		$this->view->setVar('title', $title);
		$this->view->start();

		return true;
	}
}
 
?>