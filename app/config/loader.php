<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

$loader = new \Phalcon\Loader();

$loader->registerNamespaces([
	'App\Models'	=> APP_PATH.$config->application->baseDir.$config->application->modelsDir,
	'App'			=> APP_PATH.$config->application->baseDir.$config->application->libraryDir
]);

$loader->register();