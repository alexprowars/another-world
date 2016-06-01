<?php

namespace App\Models;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Model;

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