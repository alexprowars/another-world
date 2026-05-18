<?php

namespace App\Engine\Map\Arena;

use App\Exceptions\Exception;
use App\Models\User;
use App\Services\BattleService;
use Inertia\Inertia;

class Training
{
	public function __invoke()
	{
		$request = request();

		if ($request->has('fight')) {
			$enemy = User::findOne($request->integer('fight'));

			if (!$enemy) {
				throw new Exception('Противник не найден');
			}

			BattleService::fight($request->user(), $enemy, 2);

			return to_route('battle');
		}

		$players = [];

		$list = User::query()
			->where('room', 2)
			->where('rank', 60)
			->where('level', '>=', $request->user()->level)
			->orderBy('level')
			->get();

		foreach ($list as $user) {
			$players[] = $user->only(['id', 'name', 'rank', 'level']);
		}

		return Inertia::render('Map/Arena/Training', [
			'players' => $players
		]);
	}
}
