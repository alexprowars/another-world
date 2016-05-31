<?php

include_once(APP_PATH."app/functions.php");

if (isset($application))
{
	$application->registerModules([
		'game' =>
		[
			'className'	=> 'App\Game\Module',
			'path'		=> APP_PATH.'app/modules/game/Module.php',
		],
	]);
}

$result = $di->get('cache')->get('app_config');

if ($result === null)
{
	$result = [];

	$loads = $di->get('db')->query("SELECT `key`, `value` FROM game_config");

	while ($load = $loads->fetch())
		$result[$load['key']] = $load['value'];

	$di->get('cache')->save('app_config', $result, 3600);
}

$di->get('config')->merge(new \Phalcon\Config(['app' => $result]));

if ($di->has('auth'))
{
	$di->get('auth')->checkExtAuth();
}

define('VERSION', '0.1');
define('DB_PREFIX', 'game_');
 
?>