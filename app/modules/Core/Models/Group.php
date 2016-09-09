<?php

namespace Sky\Core\Models;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Model;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * @method static Group[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Group findFirst(mixed $parameters = null)
 */
class Group extends Model
{
	public $id;
	public $title;
	public $locked;
	public $metadata;

	const ROLE_ADMIN = 1;
	const ROLE_ANONYM = 2;
	const ROLE_USER = 3;

	public function getSource()
	{
		return DB_PREFIX."groups";
	}

	public function isSystem ()
	{
		return ($this->id <= 3);
	}
}