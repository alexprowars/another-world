<?php


/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */


use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Loader;
use Sky\Core\Access\Auth;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View\Engine\Volt;
use Game\Models\User;

/**
 * @var $di DiInterface
 * @var $eventsManager EventsManager
 */

$config = $di->getShared('config');
$loader = $di->getShared('loader');

$loader->registerClasses([
		'Game\Database' => ROOT_PATH.$config->application->baseDir.'modules/Game/Classes/Database.php'
], true);

/** @noinspection PhpUnusedParameterInspection */
$eventsManager->attach('core:beforeAuthCheck', function ($event, Auth $auth)
{
	\Sky\Core\Modules::init('game');

	if (!$auth->isAuthorized())
	{
		$auth->addPlugin('\Game\Auth\Plugins\Ulogin');
		$auth->addPlugin('\Game\Auth\Plugins\Vk');
	}
});

/** @noinspection PhpUnusedParameterInspection */
$eventsManager->attach('core:afterAuthCheck', function ($event, Auth $auth, User $user) use ($di)
{
	if (!$auth->isAuthorized())
		return;

	$ip = sprintf("%u", ip2long($this->request->getClientAddress()));

	if ($user->ip != $ip)
	{
		$user->ip = $ip;

		$this->db->insertAsDict(
			"game_log_ip",
			[
				'id'	=> $user->id,
				'time'	=> time(),
				'ip'	=> $ip
			]
		);
	}

	if ($di->getShared('router')->getControllerName() != 'banned')
	{
		$game = $di->getShared('game');
		$url = $di->getShared('url');

		if ($user->banned > time())
			die('Ваш аккаунт заблокирован. Срок окончания блокировки: '.$game->datezone("d.m.Y H:i:s", $user->banned).'<br>Для получения дополнительной информации зайдите <a href="'.$url->get('banned/').'">сюда</a>');
		elseif ($user->banned > 0 && $user->banned < time())
		{
			$this->db->delete('game_banned', 'who = ?', [$user->id]);

			$user->banned = 0;
		}
	}

	if ($user->hasChanged())
		$user->update();
});

/** @noinspection PhpUnusedParameterInspection */
$eventsManager->attach('view:afterEngineRegister', function ($event, Volt $volt)
{
	$compiler = $volt->getCompiler();

	$compiler->addFilter('floor', 'floor');
	$compiler->addFilter('round', 'round');
	$compiler->addFilter('ceil', 'ceil');

	$compiler->addFunction('number_format', 'number_format');
	$compiler->addFunction('in_array', 'in_array');
	$compiler->addFunction('long2ip', 'long2ip');
	$compiler->addFunction('replace', 'str_replace');
	$compiler->addFunction('preg_replace', 'preg_replace');
	$compiler->addFunction('md5', 'md5');
	$compiler->addFunction('min', 'min');
	$compiler->addFunction('max', 'max');
	$compiler->addFunction('floor', 'floor');
	$compiler->addFunction('ceil', 'ceil');
	$compiler->addFunction('array_search', 'array_search');
	$compiler->addFunction('is_email', 'is_email');
	$compiler->addFunction('htmlspecialchars', 'htmlspecialchars');
	$compiler->addFunction('rand', 'mt_rand');
	$compiler->addFunction('implode', 'implode');
	$compiler->addFunction('http_build_query', 'http_build_query');
	$compiler->addFunction('getWidth', 'GetWPers');
});

/** @noinspection PhpUnusedParameterInspection */
$eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception)
{
	/**
	 * @var \Phalcon\Mvc\Dispatcher $dispatcher
	 * @var \Phalcon\Mvc\Dispatcher\Exception $exception
	 */
	switch ($exception->getCode())
	{
		case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
		case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:

			if ($dispatcher->getControllerName() == $dispatcher->getPreviousControllerName() && $dispatcher->getActionName() == $dispatcher->getPreviousActionName())
				return true;

			$dispatcher->forward(
				[
					'module'		=> 'game',
					'controller'	=> 'error',
					'action'		=> 'notFound',
					'namespace'		=> 'Game\Controllers'
				]
			);

			return false;
	}

	return true;
});

define('VERSION', '0.1');