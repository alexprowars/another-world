<?php

namespace App\Engine\Locations;

use App\Http\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class LocationController extends Controller
{
	protected function prepareAction(Request $request): void
	{
		$request->merge(['action' => $request->route('locationAction')]);
	}

	protected function redirectToLocation(array $query = []): RedirectResponse
	{
		return redirect($this->user->currentLocation()->url($query));
	}
}
