<?php

namespace App\Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class TransfersController extends Application
{
	public function initialize ()
	{
		$this->tag->setTitle('Передача предметов');

		parent::initialize();
	}

    public function indexAction()
    {
		$message = '';

		if ($this->request->has('login') && ($this->user->level >= 6 || $this->user->isAdmin()))
		{
			$objects = array();

			$login = htmlspecialchars($this->request->get('login'));
			
			$info = $this->db->query("SELECT id, username, room, onlinetime, rank, tribe, id, level FROM game_users WHERE ".(is_numeric($login) ? "id = ".intval($login) : "username = '".addslashes($login)."'")." LIMIT 1")->fetch();

			if (isset($info['id']) && $info['id'] != $this->user->getId())
			{
				if ($this->request->has('transfer'))
				{
					$object = $this->db->query("SELECT id, inf, present FROM game_objects WHERE user_id = '" . $this->user->getId() . "' AND id = '" . $this->request->get('transfer', 'int') . "' AND onset = 0")->fetch();

					if (isset($object['id']))
					{
						$object_inf = explode("|", $object['inf']);

						if ($object_inf[5] == 0 || ($object_inf[5] == 1 && ($this->user->isAdmin() || $this->user->rank == 100 ||$this->user->rank == 30)))
						{
							if (!$object['present'])
							{
								if ((time() - $info['onlinetime'] <= 400) || $info['rank'] != 70)
								{
									$success = $this->db->query("UPDATE game_objects SET user_id = '" . $info['id'] . "' WHERE id = '" . $object['id'] . "'");

									if ($success)
									{
										$this->db->insertAsDict(
											"game_log_transfers",
											array
											(
												'time' 		=> time(),
												'ip'		=> convertIp($this->request->getClientAddress()),
												'from_id' 	=> $this->user->getId(),
												'user_id' 	=> $info['id'],
												'credits' 	=> 0,
												'object_id' => $object['id'],
												'comment' 	=> '',
											)
										);

										$this->game->insertInChat("<b><u>" . $this->user->username . "</u></b> передал Вам предмет <b><u>" . $object_inf[1] . "</u></b>", $info['username']);

										$message = "Предмет <b><u>" . $object_inf[1] . "</u></b> удачно передан к <b><u>" . $info['username'] . "</u></b>";
									}
								}
								else
									$message = "<b style='COLOR: Red'>Персонаж находится не в игре!</b>";
							}
							else
								$message = "<b style='COLOR: Red'>Вы не можете передавать подарки!</b>";
						}
						else
							$message = "<b style='COLOR: Red'>Вы не можете передавать Артефакты!</b>";
					}
					else
						$message = "<b style='COLOR: Red'>Предмет не найден в Вашем рюкзаке!</b>";
				}

				if ($this->request->hasPost('credits'))
				{
					$credits = $this->request->getPost('credits', 'int', 0);

					if ($credits < 0)
						$message = "<b>Укажите правильную сумму !</b>";
					else
					{
						$comment = $this->request->getPost('comment', 'string', '');

						if ($this->user->credits < $credits)
							$message = "<b>У Вас недостаточно средств для передачи!</b>";
						elseif ($comment == '')
							$message = "<b>Укажите причину!</b>";
						else
						{
							$this->user->credits -= $credits;

							$success = $this->db->query("UPDATE game_users t1, game_users t2 SET t1.credits = t1.credits - " . $credits . ", t2.credits = t2.credits + " . $credits . " WHERE t1.id = '" . $this->user->getId() . "' AND t2.id ='" . $info['id'] . "' AND t1.credits >= " . $credits . "");

							if ($success)
							{
								$this->db->insertAsDict(
									"game_log_transfers",
									array
									(
										'time' 		=> time(),
										'ip'		=> convertIp($this->request->getClientAddress()),
										'from_id' 	=> $this->user->getId(),
										'user_id' 	=> $info['id'],
										'credits' 	=> $credits,
										'object_id' => 0,
										'comment' 	=> $comment,
									)
								);

								$this->game->insertInChat("Персонаж <b><u>" . $this->user->username . "</u></b> передал Вам <b><u>" . $credits . "</u></b> зол.", $info['username']);

								$message = "Вы удачно перевели \"<b>" . $credits . "</b>\" кредитов к персонажу <b><u>" . $info['username'] . "</u></b>";
							}
						}
					}
				}

				$objects = $this->user->getSlot()->getInventoryObjects();
			}

			$this->view->setVar('info', $info);
			$this->view->setVar('objects', $objects);
		}

		if ($this->user->level < 6 && !$this->user->isAdmin())
			$message = 'Передачи разрешены только персонажам начиная с 6 уровня!';

		$this->view->setVar('message', $message);
	}
}
?>