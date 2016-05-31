<?

/**
 * @var \App\Controllers\MapController $this
 */


if ($this->request->has('room'))
{
	$room = $this->request->get('room', 'int');

	if ($this->user->room == 104)
	{
		$new_set = 0;

		switch ($room)
		{
			case 10: $new_set = 10; break; // Лавка мага
			case 12: $new_set = 12; break; // Игорный дом
			case 13: $new_set = 13; break; // Сувенирная лавка
			case 16: $new_set = 16; break; // Центр занятости
			case 35: $new_set = 35; break; // Таверна

			case 103: $new_set = 103; break; // Торговая площадь
			case 105: $new_set = 105; break; // Промышленная улица
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

$this->view->pick('shared/city/1_street_3');
$this->view->setVar('city', $city);

?>