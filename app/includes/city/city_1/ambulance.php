<?

/**
 * @var \Game\Controllers\MapController $this
 */

if ($this->user->level < 4)
	$regTime = 180;
else
	$regTime = 360;

$hp_max = $this->user->hp_max;

$time = round((1 - ($this->user->hp_now / $hp_max)) * $regTime);

// Старт лечения жизней
if ($this->request->has('use'))
{
	if (!$this->user->r_time && $this->user->vitality > 0)
	{
		if ($time > 0)
		{
			$this->user->r_time = time() + $time;
			$this->user->r_type = 2;
			$this->user->update();
		}
	}
}

// Лечение травмы
if ($this->request->has('puse') && $this->user->travma > time() && $this->user->credits >= 200)
{
	$this->db->query("DELETE FROM game_users_effects WHERE user_id = '" . $this->user->id . "' AND type = '3'");

	$this->user->update([
		'travma' => 0,
		't_level' => 0,
		't_type' => 0,
		'credits' => $this->user->credits - 200,
		'room' => 1
	]);

	$this->game->insertInChat("Лечение окончено! Вы транспортированы в помещение: <b><u>Общий зал</u></b>", $this->user->username, true);

	$this->response->redirect('/map/');
	$this->response->send();
}

// Идет лечение
if ($this->user->r_time > 0)
{
	if ($this->user->vitality < 1)
		$this->user->update(['r_time' => 0, 'r_type' => 0]);

	if ($time <= 0)
	{
		$this->user->update(['r_time' => 0, 'r_type' => 0, 'room' => 1]);

		$this->game->insertInChat("Лечение окончено! Вы транспортированы в помещение: <b><u>Общий зал</u></b>", $this->user->username);

		$this->response->redirect('/map/');
		$this->response->send();
	}

	$hp = $hp_max - (round(($this->user->r_time - time()) * $hp_max / $regTime));

	if ($hp <= $hp_max)
	{
		$this->user->hp_now = $hp;
		$this->user->update();
	}
	else
	{
		$this->user->update(['r_time' => 0, 'r_type' => 0, 'room' => 1, 'hp_now' => $hp_max]);

		$this->game->insertInChat("Лечение окончено! Вы транспортированы в помещение: <b><u>Общий зал</u></b>", $this->user->username);

		$this->response->redirect('/map/');
		$this->response->send();
	}
}

$this->view->pick('shared/city/1_ambulance');
$this->view->setVar('time', $time);