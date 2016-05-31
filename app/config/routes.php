<?php

use Phalcon\Mvc\Router;

$router = new Router(true);
$router->removeExtraSlashes(true);

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
 
?>