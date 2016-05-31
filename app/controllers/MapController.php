<?
namespace App\Controllers;

class MapController extends ControllerBase
{
    public function indexAction()
    {
		if ($this->request->has('refer'))
		{
			if ($this->user->r_time > 0 || $this->user->r_type > 0)
				$this->game->setMessage('Нельзя перемещаться по городу пока занят работой');
			else
			{
				$refer = $this->request->get('refer', 'int');

				if ($refer != $this->user->room)
					$this->game->setMessage('Типа читанул?');
				else
				{
					$this->setRoom($refer);
				}
			}
		}

		$this->checkRoom($this->user->room);
    }

	private function setRoom ($oldRoom)
	{
		$new_room = 0;

		switch ($oldRoom)
		{
			case  2:
			case  9:
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
			case  8:
			case 29:
			case 22:
				$new_room = 101;
				break;
			case  7:
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

		if ($new_room != 0)
		{
			$this->db->updateAsDict(
			   	"game_users",
				['room' => $new_room],
			   	"id = ".$this->user->getId()
			);

			$this->user->room = $new_room;
		}
	}

	public function checkRoom ($roomId)
	{
		switch ($roomId)
		{
			case   1:
				include(APP_PATH . 'app/includes/city/city.php');
				break; // Арена
			case   2:
				include(APP_PATH . 'app/includes/city/city_1/trening.php');
				break; // Тренировочная
			case   7:
				include(APP_PATH . 'app/includes/city/city_1/shop.php');
				break; // Магазин
			case   8:
				include(APP_PATH . 'app/includes/city/city_1/ambulance.php');
				break; // Больница
			case   9:
				include(APP_PATH . 'app/includes/city/city_1/academy.php');
				break; // Академия
			case 10:
				include(APP_PATH . 'app/includes/city/city_1/mshop.php');
				break; // Лавка мага
			case 11:
				include(APP_PATH . 'app/includes/city/city_1/repair.php');
				break; // Кузница
			case 12:
				include(APP_PATH . 'app/includes/city/city_1/gamblinghouse.php');
				break; // Игорный дом
			case 13:
				include(APP_PATH . 'app/includes/city/city_1/gshop.php');
				break; // Сувениры
			case 14:
				include(APP_PATH . 'app/includes/city/city_1/administ.php');
				break; // Админка
			case 16:
				include(APP_PATH . 'app/includes/city/city_1/works.php');
				break; // Центр занятости
			case 17:
				include(APP_PATH . 'app/includes/city/city_1/bank.php');
				break; // Банк
			case 19:
				include(APP_PATH . 'app/includes/city/city_1/ambar.php');
				break; // Приём ресоф
			case 20:
				include(APP_PATH . 'app/includes/city/city_1/komis.php');
				break; // Рынок
			case 22:
				include(APP_PATH . 'app/includes/city/city_1/brak.php');
				break; // Церковь
			case 25:
				include(APP_PATH . 'app/includes/city/city_1/pochta.php');
				break; // Почта
			case 27:
				include(APP_PATH . 'app/includes/city/city_1/znahar.php');
				break; // Знахарка
			case 28:
				include(APP_PATH . 'app/includes/city/city_1/sclad.php');
				break; // Склад
			case 29:
				include(APP_PATH . 'app/includes/city/city_1/butik.php');
				break; // Бутик
			case 35:
				include(APP_PATH . 'app/includes/city/city_1/kwest.php');
				break; // Таверна
			case 666:
				include(APP_PATH . 'app/includes/city/city_1/prison.php');
				break; // Тюрьма
			case 101:
				include(APP_PATH. 'app/includes/city/city_1/street_1.php');
				break;
			case 103:
				include(APP_PATH. 'app/includes/city/city_1/street_2.php');
				break;
			case 104:
				include(APP_PATH. 'app/includes/city/city_1/street_3.php');
				break;
			case   23:
				include(APP_PATH. 'app/includes/city/city_1/street_4.php');
				break;
			case 105:
				include(APP_PATH. 'app/includes/city/city_1/street_5.php');
				break;
			default:
				if ($this->user->room >= 200 && $this->user->room <= 370)
					include(APP_PATH . 'app/includes/city/city_1/vault.php');
				else
					include(APP_PATH . 'app/includes/city/city.php');
		}
	}

}
?>