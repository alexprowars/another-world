<?php

namespace App\Engine\World;

class World
{
	public static function cities(): array
	{
		return config('world.cities');
	}

	public static function city(string $code): array
	{
		$cities = self::cities();

		abort_unless(isset($cities[$code]), 404);

		return $cities[$code];
	}

	public static function location(string $city, string $code): array
	{
		$definition = self::city($city);

		abort_unless(isset($definition['locations'][$code]), 404);

		$location = $definition['locations'][$code];

		if (isset($location['map'])) {
			$location['connections'] = array_column(
				[...$location['places'], ...$location['exits']],
				'location',
			);
		}

		return $location;
	}

	public static function handlers(): array
	{
		return config('world.locations');
	}
}
