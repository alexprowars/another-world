<?php

namespace Sky\Core\Models;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * @method static Module[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Module findFirst(mixed $parameters = null)
 */
class Module extends Model
{
	public $id;
	public $code;
	public $sort;
	public $namespace;
	public $active;

	public function getSource()
	{
		return DB_PREFIX."modules";
	}

	public function validation ()
	{
		if ($this->code == 'core' && (int) $this->active == 1)
		{
			$this->appendMessage(new Message('Модуль ядра не может быть деактивирован', 'active', 'error'));

			return false;
		}

		return true;
	}
}