<?php

namespace App\Engine\Map\Arena;

use App\Models\User;
use App\Services\BattleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class City
{
	public function __invoke(): Response|RedirectResponse
	{
		$user = auth()->user();
		$room = request()->integer('room');

		if ($room == 23 || $room == 2 || $room == 8) {
			$existBattleRequest = BattleService::getCurrentUserRequest($user);

			if ($existBattleRequest) {
				flash('Вы подали заявку и пытаетесь убежать с поля битвы! Нехорошо...');
			} else {
				$user->room = $room;
				$user->save();

				return to_route('map');
			}
		} elseif ($room == 1) {
			return to_route('arena');
		}

		$room_1_members = User::query()
			->where('room', 1)
			->where('online', '>', now()->subMinutes(5))
			->count();

		$room_2_members = User::query()
			->where('room', 2)
			->where('online', '>', now()->subMinutes(5))
			->count();

		return Inertia::render('City', [
			'room_1_members' => $room_1_members,
			'room_2_members' => $room_2_members,
		]);
	}
}
