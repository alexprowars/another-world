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
 * @method static Access[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Access findFirst(mixed $parameters = null)
 */
class Access extends Model
{
	public $id;
	public $code;
	public $module;

	public function getSource()
	{
		return DB_PREFIX."access";
	}
}