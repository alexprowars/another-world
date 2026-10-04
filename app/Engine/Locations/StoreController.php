<?php

namespace App\Engine\Locations;

use App\Engine\World\World;

abstract class StoreController extends LocationController
{
	protected function shopId(): int
	{
		$location = $this->user->currentLocation();

		return World::location($location->city, $location->code)['shop_id'];
	}
}
