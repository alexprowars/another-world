<?

/**
 * @var \App\Game\Controllers\MapController $this
 */


$this->view->pick('shared/city/1_works');

if (isset($_GET['getproff']))
	$getproff = intval($_GET['getproff']);
if (isset($_GET['getm']))
	$getm = intval($_GET['getm']);

$message = '';

if ($this->request->hasQuery('get'))
{
	$work = $this->db->query("SELECT * FROM game_works WHERE id = " . intval($this->request->getQuery('get')) . "")->fetch();

	if (isset($work['id']) && $this->user->r_time == 0 && $this->user->r_type == 0)
	{
		$type = $this->db->query("SELECT * FROM game_works_type WHERE id = ".$work['type_id']."")->fetch();

		if ($this->user->level >= $type['level'])
		{
			if ($this->user->ustal_now >= $work['time'] / 3600 * $type['activity'])
			{
				$this->user->r_time = time() + $work['time'];
				$this->user->r_type = 4;

				$this->db->query("UPDATE `game_users` SET `r_time` = ".$this->user->r_time.", `r_type` = '".$this->user->r_type."', `credits` = `credits` + '".$work['price']."', `ustal_now` = `ustal_now` - ".($work['time'] / 3600 * $type['activity'])." WHERE `id` = '" . $this->user->id . "'");

				$message = "Процесс работы начат! По окончанию работы Вам выплятят зарплату!";
			}
			else
				$message = "Да вы батенька заработались! Идите-ка посражайтесь.";
		}
		else
			$message = "Вы не можете получить эту работу, уровень маловат!";
	}
	else
		$message = "Центр занятости не предоставляет таких услуг!";
}

if ($this->user->r_time < time() && $this->user->r_type == 4)
{
	$this->db->query("UPDATE `game_users` SET `r_time` = 0, `r_type` = '0' WHERE `id` = '" . $this->user->id . "'");

	$this->user->r_time = 0;
	$this->user->r_type = 0;
}


$works = $this->db->query("SELECT * FROM game_works WHERE 1 = 1 ORDER BY type_id, time")->fetchAll();
$types = $this->db->query("SELECT * FROM game_works_type WHERE 1 = 1 ORDER BY id")->fetchAll();

$this->view->setVar('works', $works);
$this->view->setVar('types', $types);
$this->view->setVar('message', $message);

?>