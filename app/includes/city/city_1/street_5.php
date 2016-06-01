<?

/**
 * @var \App\Game\Controllers\MapController $this
 */


if ($this->request->has('room'))
{
	$room = $this->request->get('room', 'int');

	if ($this->user->room == 105)
	{
		$new_set = 0;

		switch ($room)
		{
			case 19:
				$new_set = 19;
				break; // Склад
			case 31:
				$new_set = 31;
				break; // Карта мира
			case 36:
				$new_set = 36;
				break; // Башня смерти
			case 666:
				$new_set = 666;
				break; // Тюрьма
			case 200:
				if ($this->user->level < 2)
					echo "<script language=\"javascript\" type=\"text/javascript\">alert('Вход в подземелье только с 2 уровня!');</script>";
				else
					$new_set = 200;
				break; // Шахта
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

$this->view->pick('shared/city/1_street_5');
$this->view->setVar('city', $city);