<?

/**
 * @var \App\Controllers\MapController $this
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
					$this->db->query("UPDATE `game_users` SET proff = ".$ch['id'].", `r_time` = ".(time() + $ch['srok']).", r_type = '3', `credits` = credits - ".$ch['price']." WHERE `id` = '" . $this->user->id . "'");

					$this->user->r_time = time() + $ch['srok'];
					$this->user->r_type = 3;
					$this->user->proff = $ch['id'];

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
	$this->db->query("UPDATE game_users set r_time = 0, r_type = 0 WHERE id = ".$this->user->id."");

	$this->user->r_time = 0;
	$this->user->r_type = 0;
}

$this->view->pick('shared/city/1_academy');
$this->view->setVar('message', $message);
$this->view->setVar('professions', $this->db->query("SELECT * FROM `game_academy` WHERE `type` = '0' order by level")->fetchAll());