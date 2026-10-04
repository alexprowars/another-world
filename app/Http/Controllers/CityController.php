<?php

namespace App\Http\Controllers;

use App\Engine\World\World;
use App\Http\Controller;
use Illuminate\Http\RedirectResponse;

class CityController extends Controller
{
	public function index(string $city): RedirectResponse
	{
		World::city($city);

		$location = $this->user->currentLocation();

		abort_unless($location->city === $city, 404);

		return redirect($location->url());
	}
}
