<?php

namespace App\Engine\Map;

use App\Exceptions\Exception;
use Inertia\Inertia;
use Throwable;

class Street
{
	public function __invoke()
	{
		if ($roomId = request()->integer('room')) {
			try {
				$this->changeRoom($roomId);

				return back();
			} catch (Throwable $e) {
				flash($e->getMessage());
			}
		}

		$night = date('H');

		if ($night < 22 & $night > 7) {
			$city = 'day';
		} else {
			$city = 'night';
		}

		$user = auth()->user();

		return match ($user->room) {
			101 => Inertia::render('Map/City/Street1', ['city' => $city]),
			103 => Inertia::render('Map/City/Street2', ['city' => $city]),
			104 => Inertia::render('Map/City/Street3', ['city' => $city]),
			23 => Inertia::render('Map/City/Street4'),
			105 => Inertia::render('Map/City/Street5', ['city' => $city]),
			default => throw new Exception('Неизвестная локация'),
		};
	}

	protected function changeRoom(int $room)
	{
		$user = auth()->user();

		if ($user->room == 23 && in_array($room, [1, 9, 28, 103])) {
			$user->room = $room;
			$user->save();
		}

		if ($user->room == 103 && in_array($room, [23, 17, 25, 7, 11, 15, 20, 101, 104])) {
			$user->room = $room;
			$user->save();
		}

		if ($user->room == 101 && in_array($room, [14, 27, 8, 22, 29, 103])) {
			$user->room = $room;
			$user->save();
		}

		if ($user->room == 104 && in_array($room, [10, 12, 13, 16, 35, 103, 105])) {
			$user->room = $room;
			$user->save();
		}

		if ($user->room == 105 && in_array($room, [19, 31, 36, 666, 200, 104])) {
			if ($room == 200 && $user->level < 2) {
				throw new Exception('Вход в подземелье только с 2 уровня!');
			}

			$user->room = $room;
			$user->save();
		}
	}
}
