<?php

namespace App\Models;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Model;

class News extends Model
{
	public $id;

	public function getSource()
	{
		return DB_PREFIX."news";
	}
}

?>