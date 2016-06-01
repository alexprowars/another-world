<?

/**
 * @var \App\Game\Controllers\MapController $this
 */

$message = '';

if ($this->request->has('attack'))
{
	$message = $this->game->fight($this->request->get('userid', 'int'), 2);
}

$this->view->pick('shared/city/1_trening');
$this->view->setVar('message', $message);
$this->view->setVar('bots', $this->db->query("SELECT id, username, rank, level FROM `game_users` WHERE `room` = '2' AND `rank` = '60' AND `level` < 8 AND level >= ".$this->user->level." ORDER BY `level`")->fetchAll());