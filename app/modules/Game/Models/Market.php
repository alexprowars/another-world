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
 * @method static Market[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Market findFirst(mixed $parameters = null)
 */
class Market extends Model
{
	public $id;
	public $group_id;
	public $object_id;
	public $user_id;
	public $price;

	public function getSource()
	{
		return DB_PREFIX."market";
	}

	public function onConstruct()
	{
		$this->hasOne("item_id", 'Game\Models\Objects', "id", Array('alias' => 'Object'));
	}

	public function getObject($parameters = null)
	{
		return $this->getRelated('Object', $parameters);
	}
}