<?php

namespace App\Support;

class SocialLoginProviders
{
	private const PROVIDERS = [
		'vkid' => [
			'label' => 'VK ID',
			'icon' => 'vk',
		],
	];

	/** @return array<string, array{label: string, icon: string}> */
	public static function available(): array
	{
		$providers = [];

		foreach (self::PROVIDERS as $driver => $provider) {
			if (
				filled(config('services.' . $driver . '.client_id'))
				&& filled(config('services.' . $driver . '.client_secret'))
				&& filled(config('services.' . $driver . '.redirect'))
			) {
				$providers[$driver] = $provider;
			}
		}

		return $providers;
	}
}
