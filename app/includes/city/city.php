<?
/**
 * @var \Game\Controllers\MapController $this
 */

$message = '';

$room = $this->request->get('room', 'int', 0);

if ($room == 23 || $room == 2 || $room == 8)
{
	$userOffer = $this->db->query("select game_battle.StartTime, game_battle.BattleType, game_battle_users.Team from game_battle, game_battle_users WHERE game_battle.StartTime > ".time()." and game_battle.Status = 'Zayavka' and game_battle_users.BattleID = game_battle.BattleID and game_battle_users.FighterID = ".$this->user->getId()."")->fetch();

	if (empty($userOffer['StartTime']))
	{
		$this->db->query("UPDATE `game_users` SET room = " . $room . " WHERE id = '" . $this->user->getId() . "'");

		$this->response->redirect('map/');
		$this->view->disable();
	}
	else
		$message = "Вы подали заявку и пытаетесь убежать с поля битвы! Нехорошо...";
}
elseif ($room == 1)
{
	$this->response->redirect('battle/');
	$this->view->disable();
}

$this->view->pick('shared/city/city');

$this->view->setVar('room_1_members', $this->db->query("SELECT COUNT(`id`) AS num FROM `game_users` WHERE `room` = 1 AND `onlinetime` > '" . (time() - 180) . "'")->fetch()['num']);
$this->view->setVar('room_2_members', $this->db->query("SELECT COUNT(`id`) AS num FROM `game_users` WHERE `room` = 2 AND `onlinetime` > '" . (time() - 180) . "'")->fetch()['num']);
$this->view->setVar('message', $message);