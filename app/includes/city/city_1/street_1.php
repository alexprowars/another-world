<?

/**
 * @var \Game\Controllers\MapController $this
 */


if ($this->request->has('room'))
{
	$room = $this->request->get('room', 'int');
	
	if ($this->user->room == 101)
	{
		$new_set = 0;

		switch ($room)
		{
			case 14:
				$new_set = 14;
				break; // Админка
			case 27:
				$new_set = 27;
				break; // Домик знахаря
			case 8:
				$new_set = 8;
				break; // Больница
			case 22:
				$new_set = 22;
				break; // Храм
			case 29:
				$new_set = 29;
				break; // Бутик
			case 103:
				$new_set = 103;
				break; // Торговая площадь
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

$this->view->pick('shared/city/1_street_1');
$this->view->setVar('city', $city);