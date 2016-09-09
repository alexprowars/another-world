<?php

namespace Sky\Core\Models;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Model;
use Phalcon\Security\Random;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * @method static Session[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Session findFirst(mixed $parameters = null)
 */
class Session extends Model
{
	public $id;
	public $token;
	public $object_type;
	public $object_id;
	public $timestamp;
	public $lifetime;
	public $useragent;

	const OBJECT_TYPE_USER = 'user';

	public function getSource()
	{
		return DB_PREFIX."sessions";
	}

	public function initialize() {}

	public function onConstruct()
	{
		$this->useDynamicUpdate(true);
	}

	public function afterUpdate ()
	{
		$this->setSnapshotData($this->toArray());
	}

	public function isExist ($token)
	{
		return self::count(['conditions' => 'token = ?0', 'bind' => [$token]]) > 0;
	}

	public static function start ($type, $id, $lifetime = 3600)
	{
		$session = new self;

		$token = new Random();

		do
		{
			$key = $token->uuid();
		}
		while ($session->isExist($key));

		$success = $session->create([
			'token' 		=> $key,
			'object_type' 	=> $type,
			'object_id' 	=> $id,
			'timestamp'		=> time(),
			'lifetime'		=> $lifetime,
			'useragent'		=> $_SERVER['HTTP_USER_AGENT']
		]);
		
		return $success ? $session : false;
	}
}