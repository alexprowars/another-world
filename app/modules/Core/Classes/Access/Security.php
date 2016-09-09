<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

namespace Sky\Core\Access;

use Sky\Core\Access;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Component;

/**
 * @property \Sky\Core\Access\Auth auth
 */
class Security extends Component
{
	/**
	 * @param Event $event
	 * @param Dispatcher $dispatcher
	 * @return bool
	 */
	public function beforeExecuteRoute (/** @noinspection PhpUnusedParameterInspection */Event $event, Dispatcher $dispatcher)
	{
		$role = 'Guest';

		if (!$this->auth->isAuthorized())
		{
			$auth = $this->auth->check();

			if ($auth !== false)
			{
				$this->getDI()->set('user', $auth, true);

				if ($auth->isAdmin())
					define('SUPERUSER', 'Y');
			}
		}

		if ($this->auth->isAuthorized())
			$role = 'User';

		$this->getDI()->set('access', new Access(), true);

		$controllerName = $dispatcher->getControllerClass();

		$annotations = $this->annotations->get($controllerName);

		if (!is_object($annotations->getClassAnnotations()) || ($annotations->getClassAnnotations()->has('Private') && $role == 'User') || !$annotations->getClassAnnotations()->has('Private'))
			return true;
		elseif (is_object($annotations->getClassAnnotations()) && !$annotations->getClassAnnotations()->has('Private'))
		{
			$actions = $annotations->getMethodsAnnotations();

			if ($actions)
			{
				$actionName = $dispatcher->getActiveMethod();

				if (isset($actions[$actionName]))
				{
					if (($actions[$actionName]->has('Private') && $role == 'User') || !$actions[$actionName]->has('Private'))
						return true;
				}
			}
		}

		$this->response->redirect('')->send();
		die();
	}
}