<?php

namespace App\Engine\Locations;

use App\Engine\World\World;
use Inertia\Inertia;
use Inertia\Response;

class StreetController extends LocationController
{
	public function index(): Response
	{
		$location = $this->user->currentLocation();
		$definition = World::location($location->city, $location->code);
		$hour = now()->hour;
		$period = $hour > 7 && $hour < 22 ? 'day' : 'night';

		return Inertia::render('Map/City/Street', [
			...$definition,
			'image_path' => World::city($location->city)['image_path'] . $period . '/',
		]);
	}
}
