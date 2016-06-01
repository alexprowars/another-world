<?php

namespace App\Auth;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Mvc\User\Component;

/**
 * @property \Phalcon\Session\Bag persistent
 */
class Security extends Component
{
	public function getAcl()
	{
		//if (!isset($this->persistent->acl))
		{
			$acl = new AclList();
			$acl->setDefaultAction(Acl::DENY);

			//Register roles
			$roles = array
			(
				'users'  => new Role('Users'),
				'guests' => new Role('Guests')
			);

			foreach ($roles as $role)
			{
				$acl->addRole($role);
			}

			//Private area resources
			$privateResources = array
			(
				'pers' 		=> array('*'),
				'game'     	=> array('*'),
				'chat'     	=> array('*'),
				'edit'     	=> array('*'),
				'map'     	=> array('*'),
				'battle'    => array('*'),
				'avatar'    => array('*'),
				'library'   => array('*'),
				'tribe'   	=> array('*'),
				'transfers' => array('*'),
				'pay' 		=> array('*'),
				'admin'   	=> array('*'),
			);

			$publicResources = array
			(
				'index'     => array('*'),
				'errors'    => array('*'),
				'info'     	=> array('*'),
			);

			foreach ($privateResources as $resource => $actions)
			{
				$acl->addResource(new Resource($resource), $actions);
			}

			foreach ($publicResources as $resource => $actions)
			{
				$acl->addResource(new Resource($resource), $actions);
			}

			//Grant access to public areas to both users and guests
			foreach ($roles as $role)
			{
				foreach ($publicResources as $resource => $actions)
				{
					foreach ($actions as $action)
					{
						/**
						 * @var \Phalcon\Acl\Role $role
						 */
						$acl->allow($role->getName(), $resource, $action);
					}
				}
			}

			//Grant acess to private area to role Users
			foreach ($privateResources as $resource => $actions)
			{
				foreach ($actions as $action)
				{
					$acl->allow('Users', $resource, $action);
				}
			}

			//The acl is stored in session, APC would be useful here too
			$this->persistent->acl = $acl;
		}

		return $this->persistent->acl;
	}

	public function beforeExecuteRoute (Event $event, Dispatcher $dispatcher)
	{
		$role = 'Users';

		if (!$this->auth->isAuthorized())
		{
			$auth = $this->auth->check();

			if (!$auth)
				$role = 'Guests';

			if ($auth !== false)
			{
				$this->getDI()->set('user', $auth);

				if ($auth->isAdmin())
					define('SUPERUSER', 'Y');
			}
		}

		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();

		$acl = $this->getAcl();
		$allowed = $acl->isAllowed($role, $controller, $action);

		if ($allowed != Acl::ALLOW)
		{
			$this->response->redirect('');
			$this->response->send();
			die();

			//$dispatcher->forward(array('controller' => 'index'));

			//return false;
		}

		return true;
	}
}
 
?>