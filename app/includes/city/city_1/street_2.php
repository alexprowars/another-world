<?

/**
 * @var \App\Game\Controllers\MapController $this
 */


if ($this->request->has('room'))
{
	$room = $this->request->get('room', 'int');
	
	if ($this->user->room == 103)
	{
		$new_set = 0;

		switch ($room)
		{
			case 23:
				$new_set = 23;
				break; // Арена
			case 17:
				$new_set = 17;
				break; // Банк
			case 25:
				$new_set = 25;
				break; // Почта
			case 7:
				$new_set = 7;
				break; // Магазин
			case 11:
				$new_set = 11;
				break; // Кузница
			case 15:
				$new_set = 15;
				break;
			case 20:
				$new_set = 20;
				break; // Рынок
			case 101:
				$new_set = 101;
				break; // Королевская улица
			case 104:
				$new_set = 104;
				break; // Парк
		}
		if ($new_set != 0)
		{
			$this->db->query("UPDATE `game_users` SET `room` = " . $new_set . " WHERE `id` = '" . $this->user->getId() . "'");

			$this->response->redirect('map/');
			$this->view->disable();
		}
	}
	else
	{
		$this->response->redirect('map/');
		$this->view->disable();
	}
}

$night = date("H");

if ($night < 22 & $night > 7)
	$city = "day";
else
	$city = "night";

$this->view->pick('shared/city/1_street_2');
$this->view->setVar('city', $city);