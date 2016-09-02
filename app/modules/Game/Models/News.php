<?php

namespace Game\Models;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Model;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * @method static News[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static News findFirst(mixed $parameters = null)
 */
class News extends Model
{
	public $id;

	public function getSource()
	{
		return DB_PREFIX."news";
	}
}