<?php

namespace App\Engine\Locations;

use App\Models\User;
use Inertia\Inertia;

class PrisonController extends LocationController
{
	public function index()
	{
		$user = auth()->user();

		if ($user->prison && $user->prison->lessThanOrEqualTo(now())) {
			$user->update([
				'prison' => null,
				'prison_reason' => null,
			]);
		}

		$prisoners = User::query()
			->where('prison', '>', now())
			->orderBy('prison')
			->get(['id', 'name', 'prison', 'prison_reason'])
			->map(fn (User $prisoner) => [
				'id' => $prisoner->id,
				'name' => $prisoner->name,
				'reason' => $prisoner->prison_reason,
				'until' => $prisoner->prison->toAtomString(),
			]);

		return Inertia::render('Map/Prison', [
			'until' => $user->prison?->toAtomString(),
			'reason' => $user->prison_reason,
			'prisoners' => $prisoners,
		]);
	}
}
