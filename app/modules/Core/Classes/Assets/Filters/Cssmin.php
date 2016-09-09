<?php

namespace Sky\Core\Assets\Filters;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Sky\Core\Assets\FilterInterface;

class Cssmin implements FilterInterface
{
	/**
	 * @param $content string
	 * @param $resource \Phalcon\Assets\Resource
	 * @return mixed string
	 */
	public function filter($content, $resource)
	{
		if (strpos($resource->getPath(), '.min') === false)
		{
			$filter = new \Phalcon\Assets\Filters\Cssmin();

			return $filter->filter($content);
		}
		else
			return $content;
	}
}