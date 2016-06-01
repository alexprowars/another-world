<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use App\Auth\Auth;
use App\Database;
use App\Game;
use Phalcon\Crypt;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Logger;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model;
use Phalcon\Cache\Backend\Memcache as Cache;

$di = new FactoryDefault();

$di->set('cookies', function()
{
	$cookies = new Phalcon\Http\Response\Cookies();
	$cookies->useEncryption(false);

	return $cookies;
});

$di->set('router', function ()
{
	return require __DIR__ . '/routes.php';
});

$di->set(
	'url', function () use ($config)
	{
		/**
		 * @var Object $config
		 */
		$url = new UrlResolver();
		$url->setBaseUri($config->application->baseUri);
		return $url;
	}, true
);

$di->set(
	'db', function () use ($config)
	{
		/**
		 * @var Object $config
		 */
		$connection = new Database(array
		(
			'host'		=> $config->database->host,
			'username'	=> $config->database->username,
			'password'	=> $config->database->password,
			'dbname'	=> $config->database->dbname,
			'options'	=> [PDO::ATTR_PERSISTENT => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
		));

		return $connection;
	}
);

$di->set(
	'session', function ()
	{
		$session = new SessionAdapter();
		$session->start();
		return $session;
	}
);

$di->set('auth', function ()
{
	return new Auth();
});

$di->set('game', function ()
{
	return new Game();
});

$di->remove('transactionManager');
$di->remove('flashSession');
$di->remove('flash');
$di->remove('annotations');

$di->set('config', $config);

$di->set('crypt', function()
{
	$crypt = new Crypt();
	$crypt->setKey('2342534634563454');
	return $crypt;
});

$di->set(
	'cache', function () use ($config)
	{
		$frontCache = new \Phalcon\Cache\Frontend\None(array("lifetime" => 3600));

		/**
		 * @var Object $config
		 */
		$cache = new Cache($frontCache, array
		(
			"host" => $config->memcache->host,
			"port" => $config->memcache->port
		));


		return $cache;
	}, true
);

$di->set('modelsMetadata', function()
{
	$metaData = new \Phalcon\Mvc\Model\MetaData\Memory([
		'lifetime'		=> 86400,
		'prefix'		=> 'aw',
		'host'			=> 'localhost',
		'port'			=> 11211,
		'persistent'	=> false
	]);

	return $metaData;
});

$di->setShared(
	'storage', function ()
	{
		$registry = new \Phalcon\Registry();
		return $registry;
	}
);

$di->getShared('game')->loadGameVariables();

Model::setup(array(
	'events'			=> true,
	'columnRenaming'	=> false,
	'notNullValidations'=> false,
));