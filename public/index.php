<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;

define('APP_PATH', realpath('..').'/');

ini_set('log_errors', 'On');
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', APP_PATH.'/php_errors.log');

try
{
	$config = new \Phalcon\Config\Adapter\Ini(APP_PATH."/app/config/config.ini");

	include (APP_PATH."/app/config/loader.php");
	include (APP_PATH."/app/config/services.php");

	$application = new Application($di);

	include (APP_PATH."/app/config/bootstrap.php");

	$handle = $application->handle();

	if ($application->request->isAjax())
	{
		$application->response->setJsonContent([
			'status' => $application->game->getStatus(),
			'message' => $application->game->getMessage(),
			'html' => str_replace(Array("\t"), "", $handle->getContent()).(isset($toolbar) ? $toolbar->render() : ''),
			'data' => [
				'tutorial' => $application->game->tutorial
			]
		]);
		$application->response->setContentType('text/json', 'utf8');
		$application->response->send();
	}
	else
		echo $handle->getContent();
}
catch(\Exception $e)
{
	echo "PhalconException: ", $e->getMessage();
	echo "<br>".$e->getFile();
	echo "<br>".$e->getLine();
	echo '<pre>';
	print_r($e->getTraceAsString());
	echo '</pre>';
}