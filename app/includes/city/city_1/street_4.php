<?

/**
 * @var \App\Game\Controllers\MapController $this
 */


if ($this->request->has('room'))
{
	$room = $this->request->get('room', 'int');

	if ($this->user->room == 23)
	{
		$new_set = 0;
		switch ($room)
		{
			case 1:
				$new_set = 1;
				break; // Общий зал
			case 9:
				$new_set = 9;
				break; // Академия
			case 28:
				$new_set = 28;
				break; // Склад
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

$this->view->pick('shared/city/1_street_4');