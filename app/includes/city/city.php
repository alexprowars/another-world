<?php

use App\Engine\Battle\BattleStatus;
use App\Models\Battle;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;

$message = '';

$room = request()->integer('room');

if ($room == 23 || $room == 2 || $room == 8) {
	$existBattleRequest = Battle::query()
		->where('status', BattleStatus::WAITING)
		->whereHas('members', function (Builder $query) {
			$query->whereBelongsTo($this->user);
		})
		->exists();

	if ($existBattleRequest) {
		Inertia::share('message', 'Вы подали заявку и пытаетесь убежать с поля битвы! Нехорошо...');
	} else {
		$this->user->room = $room;
		$this->user->save();

		return redirect()->route('map');
	}
} elseif ($room == 1) {
	return redirect()->route('battle');
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
