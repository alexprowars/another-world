<?php

namespace Sky\Core;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Sky\Core\Models\Option;
use Phalcon\Di;

class Options
{
	const CACHE_KEY = 'CORE_OPTIONS';
	const CACHE_TIME = 3600;

	protected static $options = [];

	public static function get ($name, $default = "", $withoutCache = false)
	{
		if (empty($name))
			throw new \Exception("ArgumentNullException");

		if (!defined('INSTALLED'))
			return $default;

		if (!$withoutCache)
		{
			if (empty(self::$options))
				self::load();

			if (isset(self::$options[$name]))
				return self::$options[$name];
		}

		$option = Option::findFirst(["columns" => "value, type", "conditions" => "name = :name:", "bind" => ["name" => $name]]);

		if ($option)
			return $option->type == 'integer' ? (int) $option->value : $option->value;

		return $default;
	}

	private static function load ()
	{
		/**
		 * @var $cache \Phalcon\Cache\BackendInterface
		 */
		$cache = Di::getDefault()->getShared('cache');

		$data = $cache->get(self::CACHE_KEY);

		if (!is_array($data))
		{
			$options = Option::find();

			foreach ($options as $option)
				self::$options[$option->name] = is_null($option->value) ? $option->default : $option->value;

			$cache->save(self::CACHE_KEY, self::$options, self::CACHE_TIME);
		}
		else
			self::$options = $data;
	}

	public static function set ($name, $value = "")
	{
		if (empty($name))
			throw new \Exception("ArgumentNullException");

		$option = Option::findFirst(["conditions" => "name = :name:", "bind" => ["name" => $name]]);

		if ($option)
		{
			$option->value = $value;
			$option->update();

			/**
			 * @var $cache \Phalcon\Cache\BackendInterface
			 */
			$cache = Di::getDefault()->getShared('cache');

			$cache->delete(self::CACHE_KEY);
		}
		else
			throw new \Exception("OptionNotFound");
	}
}