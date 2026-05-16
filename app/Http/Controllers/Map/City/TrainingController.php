<?php

namespace App\Http\Controllers\Map\City;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Models\User;
use App\Services\BattleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingController extends Controller
{
	public function index(Request $request)
	{
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
