<?php

namespace App\Engine\Map;

use App\Models\User;
use Inertia\Inertia;

class Prison
{
	public function __invoke()
	{
		$user = auth()->user();

		if ($user->prison_until && $user->prison_until->lessThanOrEqualTo(now())) {
			$user->update([
				'prison_until' => null,
				'prison_reason' => null,
			]);
		}

		$prisoners = User::query()
			->where('prison_until', '>', now())
			->orderBy('prison_until')
			->get(['id', 'name', 'prison_until', 'prison_reason'])
			->map(fn (User $prisoner) => [
				'id' => $prisoner->id,
				'name' => $prisoner->name,
				'reason' => $prisoner->prison_reason,
				'until' => $prisoner->prison_until->toAtomString(),
			]);

		return Inertia::render('Map/Prison', [
			'until' => $user->prison_until?->toAtomString(),
			'reason' => $user->prison_reason,
			'prisoners' => $prisoners,
		]);
	}
}
