<?php

namespace App\Engine\Locations;

use App\Http\Controller;
use Illuminate\Http\RedirectResponse;

abstract class LocationController extends Controller
{
	protected function redirectToLocation(array $query = []): RedirectResponse
	{
		return redirect($this->user->currentLocation()->url($query));
	}
}
