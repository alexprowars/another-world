<?php

namespace App\Http\Controllers\Map\City;

use App\Exceptions\Exception;
use App\Http\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StreetController extends Controller
{
	public function index(Request $request)
	{
		if ($room = $request->integer('room')) {
			if ($this->user->room == 23 && in_array($room, [1, 9, 28, 103])) {
				$this->user->room = $room;
				$this->user->save();

				return to_route('map');
			}

			if ($this->user->room == 103 && in_array($room, [23, 17, 25, 7, 11, 15, 20, 101, 104])) {
				$this->user->room = $room;
				$this->user->save();

				return to_route('map');
			}

			if ($this->user->room == 101 && in_array($room, [14, 27, 8, 22, 29, 103])) {
				$this->user->room = $room;
				$this->user->save();

				return to_route('map');
			}

			if ($this->user->room == 104 && in_array($room, [10, 12, 13, 16, 35, 103, 105])) {
				$this->user->room = $room;
				$this->user->save();

				return to_route('map');
			}

			if ($this->user->room == 105 && in_array($room, [19, 31, 36, 666, 200, 104])) {
				if ($room == 200 && $this->user->level < 2) {
					throw new Exception('Вход в подземелье только с 2 уровня!');
				}

				$this->user->room = $room;
				$this->user->save();

				return to_route('map');
			}

			return back();
		}

		$night = date('H');

		if ($night < 22 & $night > 7) {
			$city = 'day';
		} else {
			$city = 'night';
		}

		switch ($this->user->room) {
			case 101:
				return Inertia::render('Map/City/Street1', ['city' => $city]);
			case 103:
				return Inertia::render('Map/City/Street2', ['city' => $city]);
			case 104:
				return Inertia::render('Map/City/Street3', ['city' => $city]);
			case 23:
				return Inertia::render('Map/City/Street4');
			case 105:
				return Inertia::render('Map/City/Street5', ['city' => $city]);
			default:
				return to_route('map');
		}
	}
}
