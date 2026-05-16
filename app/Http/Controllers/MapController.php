<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Controllers\Map\City\StreetController;
use Illuminate\Http\Request;

class MapController extends Controller
{
	public function index(Request $request)
	{
		$user = $request->user();

		if ($request->has('refer')) {
			if ($user->r_date || $user->r_type) {
				throw new Exception('Нельзя перемещаться по городу пока занят работой');
			}

			$refer = $request->integer('refer');

			if ($refer != $user->room) {
				throw new Exception('Типа читанул?');
			}

			$this->setRoom($refer);
		}

		return $this->checkRoom($user->room);
	}

	private function setRoom($oldRoom)
	{
		$new_room = 0;

		switch ($oldRoom) {
			case 2:
			case 9:
			case 28:
				$new_room = 23;
				break;
			case 40:
			case 42:
			case 43:
			case 44:
			case 45:
			case 46:
			case 47:
			case 48:
			case 49:
			case 51:
			case 52:
				$new_room = 31;
				break;
			case 30:
			case 14:
			case 27:
			case 8:
			case 29:
			case 22:
				$new_room = 101;
				break;
			case 7:
			case 11:
			case 15:
			case 17:
			case 20:
			case 25:
				$new_room = 103;
				break;
			case 10:
			case 12:
			case 13:
			case 16:
			case 26:
			case 35:
				$new_room = 104;
				break;
			case 19:
			case 36:
			case 200:
			case 666:
				$new_room = 105;
				break;
			case 33:
			case 38:
				$new_room = 106;
				break;
			case 39:
			case 50:
				$new_room = 107;
				break;
		}

		if ($new_room != 0) {
			$this->user->room = $new_room;
			$this->user->save();
		}
	}

	public function checkRoom($roomId)
	{
		switch ($roomId) {
			case 1:
				return include(app_path('/includes/city/city.php'));
				break; // Арена
			case 2:
				return to_route('map.arena.training');
				return include(app_path('/includes/city/city_1/trening.php'));
				break; // Тренировочная
			case 7:
				return to_route('map.city.shop');
			case 8:
				return include(app_path('/includes/city/city_1/ambulance.php'));
				break; // Больница
			case 9:
				return include(app_path('/includes/city/city_1/academy.php'));
				break; // Академия
			case 10:
				return include(app_path('/includes/city/city_1/mshop.php'));
				break; // Лавка мага
			case 11:
				return include(app_path('/includes/city/city_1/repair.php'));
				break; // Кузница
			case 12:
				return include(app_path('/includes/city/city_1/gamblinghouse.php'));
				break; // Игорный дом
			case 13:
				return include(app_path('/includes/city/city_1/gshop.php'));
				break; // Сувениры
			case 14:
				return include(app_path('/includes/city/city_1/administ.php'));
				break; // Админка
			case 16:
				return include(app_path('/includes/city/city_1/works.php'));
				break; // Центр занятости
			case 17:
				return include(app_path('/includes/city/city_1/bank.php'));
				break; // Банк
			case 19:
				return include(app_path('/includes/city/city_1/ambar.php'));
				break; // Приём ресоф
			case 20:
				return include(app_path('/includes/city/city_1/komis.php'));
				break; // Рынок
			case 22:
				return include(app_path('/includes/city/city_1/brak.php'));
				break; // Церковь
			case 25:
				return include(app_path('/includes/city/city_1/pochta.php'));
				break; // Почта
			case 27:
				return include(app_path('/includes/city/city_1/znahar.php'));
				break; // Знахарка
			case 28:
				return include(app_path('/includes/city/city_1/sclad.php'));
				break; // Склад
			case 29:
				return include(app_path('/includes/city/city_1/butik.php'));
				break; // Бутик
			case 35:
				return include(app_path('/includes/city/city_1/kwest.php'));
				break; // Таверна
			case 666:
				return include(app_path('/includes/city/city_1/prison.php'));
				break; // Тюрьма
			case 101:
				return to_route('map.city.street', [1]);
			case 103:
				return to_route('map.city.street', [2]);
			case 104:
				return to_route('map.city.street', [3]);
			case 23:
				return to_route('map.city.street', [4]);
			case 105:
				return to_route('map.city.street', [5]);
			default:
				if ($this->user->room >= 200 && $this->user->room <= 370) {
					return include(app_path('/includes/city/city_1/vault.php'));
				} else {
					return include(app_path('/includes/city/city.php'));
				}
		}
	}
}
