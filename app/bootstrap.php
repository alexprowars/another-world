<?php

if (!defined('ROOT_PATH'))
    define('ROOT_PATH', dirname(dirname(__FILE__)));

try
{
	require_once(ROOT_PATH."/app/modules/Core/Classes/Application/Services.php");
	require_once(ROOT_PATH."/app/modules/Core/Classes/Application.php");

	$application = new Sky\Core\Application();
	$application->run();

	echo $application->getOutput();
}
catch(\Exception $e)
{
	echo '<pre style="margin:25px;padding:25px">';
    echo 'Exception: ', $e->getMessage();
	echo '<br>'.$e->getFile();
	echo '<br>'.$e->getLine();
	echo '<pre style="margin:10px 0">';

	if (defined('SUPERUSER'))
		print_r($e->getTraceAsString());

	echo '</pre>';
}