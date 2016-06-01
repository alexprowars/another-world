<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Router;

$router = new Router(true);
$router->removeExtraSlashes(true);
$router->setDefaultModule('game');

$router->add('/admin/:action/action/([a-zA-Z0-9_-]+)/:params', array
(
	'controller' 	=> 'admin',
	'action' 		=> 1,
	'mode' 			=> 2,
	'params' 		=> 3
));

$router->add('/', array
(
	'controller' 	=> 'index',
	'action' 		=> 'news',
));

$router->handle();

return $router;