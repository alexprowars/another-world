<?php

namespace Sky\Core\Helpers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Di;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Cache
{
	private static $cacheDirs = [
		'/public/assets/cache/',
		'/app/cache/views/'
	];

	static function clearAll ()
	{
		self::clearFilesCache();
		self::clearApplicationCache();
	}

	static function clearFilesCache ()
	{
		foreach (self::$cacheDirs as $dir)
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOT_PATH.$dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($files as $file)
			{
				$todo = ($file->isDir() ? 'rmdir' : 'unlink');
				$todo($file->getRealPath());
			}
		}
	}

	static function clearApplicationCache ()
	{
		if (function_exists('apc_clear_cache'))
			apc_clear_cache();

		$di = Di::getDefault();

		if (!$di->has('cache') && $di->has('app'))
		{
			$application = $di->getShared('app');

			$application->initCache($di, $di->getShared('config'));
		}

		if ($di->has('cache'))
		{
			/**
			 * @var $cache \Phalcon\Cache\BackendInterface
			 */
			$cache = $di->getShared('cache');

			$cache->flush();
		}
	}
}