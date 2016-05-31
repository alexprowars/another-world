<?php
namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * Class Objects
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
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
		$this->hasOne("item_id", 'App\Models\Objects', "id", Array('alias' => 'Object'));
	}

	public function getObject($parameters = null)
	{
	 	return $this->getRelated('Object', $parameters);
	}
}

?>