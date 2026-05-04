<?php

namespace Game\Auth\Plugins;

/**
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Assets\Manager assets
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Session\Adapter\Memcache session
 * @property \Phalcon\Http\Response\Cookies cookies
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Config|\stdClass config
 * @property \Sky\Core\Access\Auth auth
 */
interface AuthInterface
{
	public function isAuthorized ();
	public function login ();
	public function register ();
}