<?php

namespace App;

use Spatie\LaravelSettings;

class Settings extends LaravelSettings\Settings
{
	public int $usersTotal;
	public int $usersOnline;

	public static function group(): string
	{
		return 'general';
	}
}
