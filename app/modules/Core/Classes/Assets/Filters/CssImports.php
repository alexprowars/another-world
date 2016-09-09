<?php

namespace Sky\Core\Assets\Filters;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Sky\Core\Assets\FilterInterface;
use Phalcon\Tag;

class CssImports implements FilterInterface
{
	/**
	 * @param $content string
	 * @param $resource \Phalcon\Assets\Resource
	 * @return mixed string
	 */
	public function filter($content, $resource)
	{
		$basePath = Tag::getUrlService()->getStaticBaseUri();

		$path = $basePath.dirname($resource->getPath());

		$content = preg_replace_callback(
			'#([;\s:]*(?:url|@import)\s*\(\s*)(\'|"|)(.+?)(\2)\s*\)#si',
			create_function('$matches', 'return $matches[1].Sky\Core\Assets\Manager::replaceUrlCSS($matches[3], $matches[2], "'.addslashes($path).'").")";'),
			$content
		);

		$content = preg_replace_callback(
			'#(\s*@import\s*)([\'"])([^\'"]+)(\2)#si',
			create_function('$matches', 'return $matches[1].Sky\Core\Assets\Manager::replaceUrlCSS($matches[3], $matches[2],"'.addslashes($path).'");'),
			$content
		);
		
		return $content;
	}
}