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
 * @method static Shop[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Shop findFirst(mixed $parameters = null)
 */
class Shop extends Model
{
	public $id;
	public $name;

	public function getSource()
	{
		return DB_PREFIX."shop";
	}

	public function onConstruct()
	{
		$this->hasMany("id", 'Game\Models\ShopItems', "shop_id", Array('alias' => 'ShopItems'));
	}

	public function getShopItems($parameters = null)
	{
		return $this->getRelated('ShopItems', $parameters);
	}
}