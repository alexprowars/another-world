<?php

namespace App\Engine\Locations;

use App\Engine\Services\BattleService;
use App\Exceptions\Exception;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TrainingArenaController extends LocationController
{
	public function index(): Response
	{
		$players = User::query()
			->where('location', $this->user->location)
			->where('rank', 60)
			->where('is_clone', false)
			->where('level', '>=', $this->user->level)
			->orderBy('level')
			->get(['id', 'name', 'rank', 'level']);

		return Inertia::render('Map/Arena/Training', ['players' => $players]);
	}

	public function store()
	{
		$data = request()->validate([
			'enemy_id' => ['required', 'integer', 'min:1'],
		]);

		$enemy = User::query()->find($data['enemy_id']);

		if (!$enemy) {
			throw ValidationException::withMessages(['fight' => 'Противник не найден']);
		}

		try {
			BattleService::fight($this->user, $enemy, 2);
		} catch (Exception $e) {
			throw ValidationException::withMessages(['fight' => strip_tags($e->getMessage())]);
		}

		return to_route('battle');
	}
}
