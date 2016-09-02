<?

/**
 * @var \Game\Controllers\MapController $this
 */


if ($this->user->t_time < time())
{
	$this->db->query("UPDATE game_users SET t_time=0 WHERE id = ".$this->user->id."");

	$this->user->t_time = 0;
}

$this->view->pick('shared/city/1_prison');
$this->view->setVar('list', $this->db->query("SELECT u.username, u.t_time, b.theme FROM game_users u, game_banned b WHERE u.t_time > ".time()." AND b.who = u.id order by u.t_time")->fetchAll());