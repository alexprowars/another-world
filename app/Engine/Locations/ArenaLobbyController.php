<?php

namespace App\Engine\Locations;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ArenaLobbyController extends LocationController
{
	public function index(): Response
	{
		$location = $this->user->currentLocation();

		return Inertia::render('Map/Arena/Lobby', [
			'arena_members' => User::query()
				->where('location', $location->value())
				->where('online', '>', now()->subMinutes(5))
				->count(),
			'training_members' => User::query()
				->where('location', $location->inCity('training-arena')->value())
				->where('online', '>', now()->subMinutes(5))
				->count(),
		]);
	}
}
