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
 * @method static Option[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Option findFirst(mixed $parameters = null)
 */
class Option extends Model
{
	public $id;
	public $name;
	public $title;
	public $value;
	public $group;
	public $type;
	public $default;
	public $description;

	public function getSource()
	{
		return DB_PREFIX."options";
	}

	public function initialize() {}

	public function onConstruct()
	{
		$this->useDynamicUpdate(true);
	}

	public function afterUpdate ()
	{
		$this->setSnapshotData($this->toArray());
	}
}