<?

/**
 * @var \Game\Controllers\MapController $this
 */

$message = '';

if ($this->request->hasQuery('learn'))
{
	$id = $this->request->getQuery('learn', 'int');

	if ($id > 0)
	{
		$ch = $this->db->query("SELECT * FROM game_academy WHERE id = " . $id . " AND type = 0")->fetch();

		if (isset($ch['id']))
		{
			if ($this->user->isFree())
			{
				if ($this->user->credits < $ch['price'])
					$message = "Недостаточно кредитов!";
				elseif ($this->user->level >= $ch['level'])
					$message = "Вы не можете получить эту профессию, уровень маловат!";
				else
				{
					$this->user->r_time = time() + $ch['srok'];
					$this->user->r_type = 3;
					$this->user->proff = $ch['id'];
					$this->user->credits -= $ch['price'];

					if ($this->user->update())
						$message = "Процесс обучения начат! По окончанию обучения Вы станете высококвалицицированным специалистом!";
				}
			}
			else
				$message = "Вы не можете заниматься сразу двумя делами!";
		}
		else
			$message = "Академия не предоставляет таких услуг!";
	}
	else
		$message = "Академия не предоставляет таких услуг!";
}

if ($this->user->r_type == 3 && $this->user->r_time < time())
{
	$this->user->r_time = 0;
	$this->user->r_type = 0;
	$this->user->update();
}

$this->view->pick('shared/city/1_academy');
$this->view->setVar('message', $message);
$this->view->setVar('professions', $this->db->query("SELECT * FROM `game_academy` WHERE `type` = '0' order by level")->fetchAll());