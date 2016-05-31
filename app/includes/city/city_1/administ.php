<?

/**
 * @var \App\Game\Controllers\MapController $this
 */

$message = '';

if ($this->request->has('podat'))
{
	$pg = $this->db->query("SELECT `status` FROM `game_tribes_request` WHERE `user_id` = '".$this->user->id."'");

	if ($pg->numRows() > 0)
		$message = "Заявка была подана ранее!";
	elseif ($this->user->credits < 100)
		$message = "Недостаточно денег!";
	elseif ($this->user->level < 4)
		$message = "Для подачи заявки вы должны достигнуть 4 уровня в игре!";
	else
	{
		$this->db->query("INSERT INTO game_tribes_request (user_id, status, time) values ('".$this->user->id."', '0', '".time()."')");
		$this->db->query("UPDATE `person` SET `credits` = `credits` - 100 WHERE `id` = '".$this->user->id."'");

		$this->user->credits -= 100;

		$message = "Вы подали заявку на проверку у инквизиторов. Дождитесь действий инвизиторов.";
	}
}

if ($this->request->has('ubr'))
{
	$this->db->query("DELETE FROM `game_tribes_request` WHERE `user_id` = '" . $this->user->id . "'");
}

if ($this->request->hasQuery('image'))
{
	if ($this->user->credits >= 2000)
	{
		$this->db->query("UPDATE `person` SET `obraz` = 'obraz/".$this->user->sex."/".intval($this->request->getQuery('image'))."', `credits` = `credits` - 2000 WHERE `id` = '" . $this->user->id . "'");

		$this->user->credits -= 2000;

		$message = "Образ куплен!";
	}
	else
		$message = "Недостаточно еврокредитов!";
}

$otdel = $this->request->get('otdel', 'int');

$this->view->pick('shared/city/1_administ');
$this->view->setVar('message', $message);
$this->view->setVar('otdel', $otdel);

if ($otdel == 2)
{
	$this->view->setVar('list', $this->db->query("SELECT r.*, u.username FROM game_tribes_request r LEFT JOIN game_users u ON u.id = r.user_id ORDER BY r.time DESC LIMIT 15")->fetchAll());
	$this->view->setVar('request', $this->db->query("SELECT `id` FROM `game_tribes_request` WHERE `user_id` = '".$this->user->id."'")->fetch());
}
if ($otdel == 4)
{
	$this->view->setVar('list', $this->db->query("SELECT * FROM game_tribes ORDER BY id ASC")->fetchAll());
}

?>