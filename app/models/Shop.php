<?php
namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * Class Objects
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 */

class Shop extends Model
{
	public $id;
	public $name;

	public function getSource()
	{
	 	return "game_shop";
	}

	public function onConstruct()
	{
		$this->hasMany("id", "App\Models\ShopItems", "shop_id", Array('alias' => 'ShopItems'));
	}

	public function getShopItems($parameters = null)
	{
	 	return $this->getRelated('ShopItems', $parameters);
	}
}

?>