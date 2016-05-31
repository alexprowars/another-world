<?
namespace App\Controllers;

use App\Battle\Battle;

class BattleController extends ControllerBase
{
	public function initialize ()
	{
		parent::initialize();
	}

    public function indexAction()
    {
		if ($this->request->hasQuery('teleport'))
		{
			$this->db->updateAsDict(
			   	"game_users",
				['room' => 1],
			   	"id = ".$this->user->getId()
			);

			$this->user->room = 1;
		}

		if ($this->user->room == 2 && !$this->user->battle)
		{
			include(APP_PATH . 'app/includes/city/city_1/trening.php');

			return;
		}

		if ($this->user->battle > 0)
		{
			if ($this->request->get('mode', null, '') == "ajax" && $this->user->battle)
			{
				$battle = new Battle();
				$battle->init();
				$json = $battle->show();

				$this->response->setJsonContent($json);
				$this->response->setContentType('text/json', 'utf8');
				$this->response->send();
				$this->view->disable();

				die();
			}
		}
		else
		{
			$this->view->pick('battle/list');

			$offerId 	= $this->request->get('offer', 'int', 0);
			$page 		= $this->request->get('page', null, '');
			$battleType = $this->request->get('battle_type', 'int', 1);
			$battleType = min(3, max(1, $battleType));

			$this->user->getSlotsInfo();
			$this->user->checkEffects();

			$message = '';
			$alert = '';
			$userOffer = $this->getCurrentUserRequest();

			if (isset($userOffer['BattleID']))
				$battleType = $userOffer['BattleType'];

			$this->view->setVar('battleType', $battleType);

			switch ($page)
			{
				case "take_it":

					$message = $this->takeOffer($offerId);

					echo $message;

					if ($message == '')
						$userOffer = $this->getCurrentUserRequest();

					break;

				case "dismiss":

					if (isset($userOffer['BattleID']) && $userOffer['BattleType'] == 1)
					{
						if (!$userOffer['Team'])
						{
							$opponent = $this->db->query("SELECT f.FighterID, u.username, u.room FROM game_battle_users f, game_users u WHERE f.BattleID = " . $userOffer['BattleID'] . " AND f.Team = 1 AND f.FighterID = u.id")->fetch();

							$this->db->query("DELETE FROM `game_battle_users` WHERE `BattleID` = " . $userOffer['BattleID'] . " AND `FighterID` != " . $this->user->getId() . "");

							if (isset($opponent['user']))
								$this->game->insertInChat("<b>" . $this->user->username . "</b> отказал в поединке!", $opponent['username'], true);
						}
						else
							$message = "Что-то тут не так...";
					}
					else
						$message = 'Заявки несуществует или истек срок её размещения';

					break;

				case "take_back":

					if (isset($userOffer['BattleID']) && $userOffer['BattleType'] == 1)
					{
						if (!$userOffer['Team'])
						{
							$this->db->query("DELETE FROM `game_battle` WHERE `BattleID` = " . $userOffer['BattleID'] . "");
							$this->db->query("DELETE FROM `game_battle_users` WHERE `BattleID` = " . $userOffer['BattleID'] . "");
						}
						else
						{
							$this->db->query("DELETE FROM `game_battle_users` WHERE `BattleID` = " . $userOffer['BattleID'] . " AND `FighterID` = " . user::get()->getId() . "");
						}

						unset($userOffer);
					}
					else
						$message = 'Заявки несуществует или истек срок её размещения';

					break;

				case "newbattle":

					$this->createOffer($battleType);

					if ($message == '')
						$userOffer = $this->getCurrentUserRequest();

				break;
			}

			ob_start();

			switch ($battleType)
			{
				case 2:

					if ($this->user->level < 2)
						$message = 'Извините, групповые бои с 2-ого уровня';
					else
					{
						if ($page == "start" || $page == '')
							$this->startOffer($battleType);

						include(APP_PATH."app/includes/battle/show_offers_".$battleType.".php");
					}

					break;

				case 3:

					if ($this->user->level < 3)
						$message = 'Извините, хаотические бои с 3-ого уровня';
					else
					{
						if ($page == "start" || $page == '')
							$this->startOffer($battleType);

						include(APP_PATH."app/includes/battle/show_offers_".$battleType.".php");
					}

					break;

				default:

					if ($page == "start")
						$this->startOffer($battleType);

					include(APP_PATH."app/includes/battle/show_offers_".$battleType.".php");

			}

			$list = ob_get_contents();
			ob_end_clean();

			$this->view->setVar('alert', $alert);
			$this->view->setVar('message', $message);
			$this->view->setVar('list', $list);
			$this->view->setVar('battleId', (isset($userOffer['BattleID']) ? $userOffer['BattleID'] : 0));
		}
    }

	private function startOffer ($battleType)
	{
		if ($battleType == 1)
		{
			$user_offer = $this->db->query("SELECT b.BattleID, b.WeaponUsing FROM game_battle b, game_battle_users f WHERE b.StartTime > " . time() . " AND b.BattleType = 1 AND b.Status = 'Zayavka' AND f.FighterID = " . $this->user->getId() . " AND b.BattleID = f.BattleID")->fetch();

			if (isset($user_offer['BattleID']))
			{
				// Узнаём сколько человек в бою
				$participants = $this->db->query("SELECT count(distinct Team) AS num FROM game_battle_users WHERE BattleID = " . $user_offer['BattleID'] . "")->fetch()['num'];

				// Если в бою 2 чела
				if ($participants == 2)
				{
					// Записываем что бой начался
					$this->db->query("UPDATE `game_battle` SET `Status` = 'InProcess', `RaundTime` = '" . time() . "' WHERE `BattleID` = " . $user_offer['BattleID'] . "");

					$bdate = date("d.m.y H:i", time());

					// Добавляем системку в лог боя
					$this->db->query("INSERT INTO game_battle_log (HitID, BattleID, HitTime, RedComment) VALUES (0, " . $user_offer['BattleID'] . ", " . time() . ", 71)");

					// Узнаём кто в бою (ид, ник и комнату)
					$members = $this->db->query("SELECT f.FighterID, u.username, u.room FROM game_battle_users f, game_users u WHERE f.BattleID = " . $user_offer['BattleID'] . " AND f.FighterID = u.id");

					while ($member = $members->fetch())
					{
						// Добовляем перса в поединок и выводим системку в чат
						$this->db->query("UPDATE `game_users` SET `battle` = '" . $user_offer['BattleID'] . "' WHERE `id` = '" . $member['FighterID'] . "'");
						$this->game->insertInChat("Часы показывали <U>$bdate</U>, когда Ваш бой начался!", $member['username'], true);

						// Если кулачный бой то снимаем вещи с перса
						if ($user_offer['WeaponUsing'] == 1)
						{
							$this->db->query("UPDATE slots SET slots.1=0, slots.2=0, slots.3=0, slots.4=0, slots.5=0, slots.6=0, slots.7=0, slots.8=0, slots.9=0, slots.10=0, slots.11=0, slots.12=0, slots.13=0, slots.14=0, slots.15=0, slots.16=0, slots.17=0, slots.18=0, slots.19=0, slots.20=0, slots.21=0, slots.22=0 WHERE id='" . $member['FighterID'] . "'");
						}
					}

					$this->response->redirect('battle/');
					$this->view->disable();
				}
			}
		}
		elseif ($battleType == 2)
		{
			$currentOffer = $this->db->query("SELECT b.BattleID FROM game_battle b, game_battle_users f WHERE b.StartTime <= " . (time() - 10) . " AND f.FighterID = " . $this->user->getId() . " AND b.BattleType = '2' AND b.Status = 'Zayavka' AND b.BattleID = f.BattleID")->fetch();

			if (isset($currentOffer['BattleID']))
			{
				$this->db->query("UPDATE `game_battle` SET `Status` = 'InProcess', `RaundTime` = '" . time() . "' WHERE `BattleID` = " . $currentOffer['BattleID']);

				$participants = $this->db->query("SELECT count(distinct Team) AS num FROM `game_battle_users` WHERE `BattleID` = " . $currentOffer['BattleID'])->fetch()['num'];

				if ($participants >= 2)
				{
					$this->db->query("INSERT INTO game_battle_log (HitID, BattleID, HitTime, RedComment) VALUES (0, " . $currentOffer['BattleID'] . ", " . time() . ", 71)");

					$members = $this->db->query("SELECT f.FighterID, f.Team, u.username, u.room FROM game_battle_users f, game_users u WHERE f.BattleID = " . $currentOffer['BattleID'] . " AND f.FighterID = u.id");

					while ($member = $members->fetch())
					{
						$this->db->query("UPDATE `game_users` SET `battle` = " . $currentOffer['BattleID'] . ", `side` = " . $member['Team'] . " WHERE `id` = " . $member['FighterID'] . "");

						$this->game->insertInChat("Часы показывали <U>" . date("d.m.y H:i:s", time()) . "</U>, когда Ваш бой начался!", $member['username'], true);
					}

					$this->response->redirect('battle/');
					$this->view->disable();
				}
				else
				{
					$this->db->query("DELETE FROM `game_battle` WHERE `BattleID` = " . $currentOffer['BattleID'] . "");
					$this->db->query("DELETE FROM `game_battle_users` WHERE `BattleID` = " . $currentOffer['BattleID'] . "");

					$this->game->insertInChat("Ваш бой не может начаться, т.к. группа не набрана!", $this->user->username, true);
				}
			}
		}
		elseif ($battleType == 3)
		{
			$currentOffer = $this->db->query("SELECT b.BattleID, b.alg FROM game_battle b, game_battle_users f WHERE b.StartTime <= " . (time() - 10) . " AND f.FighterID = " . $this->user->getId() . " AND b.BattleType = '3' AND b.Status = 'Zayavka' AND b.BattleID = f.BattleID")->fetch();

			if (isset($currentOffer['BattleID']))
			{
				$this->db->query("UPDATE `game_battle` SET `Status` = 'InProcess', `RaundTime` = '" . time() . "' WHERE `BattleID` = " . $currentOffer['BattleID'] . "");

				$participants = $this->db->query("SELECT count(distinct FighterID) AS num FROM `game_battle_users` WHERE `BattleID` = " . $currentOffer['BattleID'])->fetch()['num'];

				$parts_num = $participants - $participants % 2;

				if ($parts_num >= 4)
				{
					$ms = 3;
					$kol = 0;

					$this->db->query("INSERT INTO game_battle_log (HitID, BattleID, HitTime, RedComment) VALUES (0, " . $currentOffer['BattleID'] . ", " . time() . ", 71)");

					if ($currentOffer['alg'] == 2)
						$members = $this->db->query("SELECT f.FighterID, f.Team, u.username, u.room, u.reit FROM game_battle_users f, game_users u WHERE f.BattleID = " . $currentOffer['BattleID'] . " AND f.FighterID = u.id ORDER BY u.reit ASC");
					else
						$members = $this->db->query("SELECT f.FighterID, f.Team, u.username, u.room, u.reit FROM game_battle_users f, game_users u WHERE f.BattleID = " . $currentOffer['BattleID'] . " AND f.FighterID = u.id ORDER by RAND()");

					if ($currentOffer['alg'] == 1 || $currentOffer['alg'] == 2)
					{
						$a = array();
						$b = array();

						while ($member = $members->fetch())
						{
							$a[] = $members['reit'];
							$b[] = array('id' => $members['FighterID'], 'user' => $members['username']);
						}

						$a1 = array();
						$a2 = array();
						$b1 = array();
						$b2 = array();

						$col = count($a);
						$c1 = 0;
						$c2 = count($a) - 1;

						while ($col > 0)
						{
							if (array_sum($a1) <= array_sum($a2))
							{
								$a1[] = $a[$c1];
								$b1[] = $b[$c1];
								$c1++;
								$col--;
							}
							if (array_sum($a1) > array_sum($a2))
							{
								$a2[] = $a[$c2];
								$b2[] = $b[$c2];
								$c2--;
								$col--;
							}
						}

						foreach ($b1 as $id => $data)
						{
							$this->db->query("UPDATE game_battle_users SET Team = '1' WHERE FighterID = " . $data['id'] . " and BattleID = " . $currentOffer['BattleID'] . "");
							$this->db->query("UPDATE game_users SET battle = " . $currentOffer['BattleID'] . ", side = 1 WHERE id = " . $data['id'] . "");

							$this->game->insertInChat("Часы показывали <U>".date("d.m.y H:i", time())."</U>, когда Ваш бой начался!", $data['user'], true);
						}

						foreach ($b2 as $id => $data)
						{
							$this->db->query("UPDATE game_users SET battle = " . $currentOffer['BattleID'] . ", side = 0 WHERE id = " . $data['id'] . "");

							$this->game->insertInChat("Часы показывали <U>".date("d.m.y H:i", time())."</U>, когда Ваш бой начался!", $data['user'], true);
						}
					}
					else
					{
						while ($member = $members->fetch())
						{
							if ($ms < 2 || $kol >= $parts_num)
							{
								$this->db->query("UPDATE game_battle_users SET Team = '1' WHERE FighterID = " . $member['FighterID'] . " and BattleID = " . $currentOffer['BattleID'] . "");
								$participant['Team'] = 1;
							}

							$ms = $kol % 4;
							$kol = $kol + 1;

							$this->db->query("UPDATE game_users SET battle = " . $currentOffer['BattleID'] . ", side = " . $member['Team'] . " WHERE id = " . $member['FighterID'] . "");

							$this->game->insertInChat("Часы показывали <U>".date("d.m.y H:i", time())."</U>, когда Ваш бой начался!", $member['username'], true);
						}
					}

					$this->response->redirect('battle/');
					$this->view->disable();
				}
				else
				{
					$this->db->query("DELETE FROM `game_battle` WHERE `BattleID` = " . $currentOffer['BattleID'] . "");
					$this->db->query("DELETE FROM `game_battle_users` WHERE `BattleID` = " . $currentOffer['BattleID'] . "");

					$this->game->insertInChat("Ваш бой не может начаться, т.к. группа не набрана!", $this->user->username, true);
				}
			}
		}
	}

	private function getCurrentUserRequest ()
	{
		return $this->db->query("SELECT b.BattleID, b.StartTime, b.BattleType, f.Team FROM game_battle b, game_battle_users f WHERE b.StartTime > " . time() . " AND b.Status = 'Zayavka' AND f.BattleID = b.BattleID AND f.FighterID = " . $this->user->getId() . "")->fetch();
	}

	private function createOffer ($battleType)
	{
		$userOffer = $this->getCurrentUserRequest();

		switch ($this->request->getPost('timeout', 'int', 0))
		{
			case 1:
				$timeout = 90;
				break;
			case 3:
				$timeout = 180;
				break;
			case 5:
				$timeout = 300;
				break;
			case 10:
				$timeout = 600;
				break;
			default:
				$timeout = 180;
				break;
		}

		$comment = htmlspecialchars($this->request->getPost('comment', null, ''));

		$message = '';

		if (isset($userOffer['BattleID']))
			$message = "Для начала с одной заявкой разберись...";
		elseif ($this->user->hp_now < $this->user->hp_max / 3)
			$message = "Вы слишком ослаблены для поединка! Восстановитесь...";
		else
		{
			switch ($battleType)
			{
				case 1:

					$this->db->insertAsDict(
						"game_battle",
						array
						(
							'StartTime' 		=> time() + 600,
							'BattleType'		=> 1,
							'RedTeamCapacity' 	=> 1,
							'BlueTeamCapacity' 	=> 1,
							'Timeout' 			=> $timeout,
							'Status' 			=> 'Zayavka',
							'Comment' 			=> $comment,
							'IsBlood' 			=> min(1, max(0, $this->request->getPost('blood', 'int', 0))),
							'WeaponUsing' 		=> min(1, max(0, $this->request->getPost('kulak', 'int', 0)))
						)
					);

					$this->db->insertAsDict(
						"game_battle_users",
						array
						(
							'BattleID' 		=> $this->db->lastInsertId(),
							'FighterID' 	=> $this->user->getId(),
							'Team' 			=> 0,
							'isBot' 		=> 0,
							'currentTime' 	=> time(),
							'TotalExpa' 	=> $this->getBaseLevelExp($this->user->level)
						)
					);

					break;

				case 2:

					if ($this->user->level < 2)
						$message = 'Извините, групповые бои с 2-ого уровня';
					else
					{
						$time_battle_start = $this->request->getPost('time_battle_start', 'int', 0);

						if ($time_battle_start != 180 && $time_battle_start != 300 && $time_battle_start != 600 && $time_battle_start != 900)
							$time_battle_start = 180;

						switch ($this->request->getPost('offer_level_1', 'int'))
						{
							case 2:
								$level_l_min = $this->user->level;
								$level_l_max = $this->user->level;
								break;
							case 3:
								$level_l_min = 0;
								$level_l_max = $this->user->level;
								break;
							case 4:
								$level_l_min = 0;
								$level_l_max = $this->user->level - 1;
								break;
							default:
								$level_l_min = 0;
								$level_l_max = 12;
						}

						switch ($this->request->getPost('offer_level_2', 'int'))
						{
							case 2:
								$level_r_min = $this->user->level;
								$level_r_max = $this->user->level;
								break;
							case 3:
								$level_r_min = 0;
								$level_r_max = $this->user->level;
								break;
							case 4:
								$level_r_min = 0;
								$level_r_max = $this->user->level - 1;
								break;
							default:
								$level_r_min = 0;
								$level_r_max = 12;
						}

						$size_left 	= $this->request->getPost('size_left', 'int', 2);
						$size_right = $this->request->getPost('size_right', 'int', 2);

						// Размеры команд
						if ($size_left < 2 || $size_left > 25)
							$size_left = 2;

						if ($size_right < 2 || $size_right > 25)
							$size_right = 2;

						$this->db->insertAsDict(
							"game_battle",
							array
							(
								'StartTime' 		=> time() + $time_battle_start,
								'BattleType'		=> 2,
								'RedTeamCapacity' 	=> $size_left,
								'BlueTeamCapacity' 	=> $size_right,
								'Timeout' 			=> $timeout,
								'Status' 			=> 'Zayavka',
								'Comment' 			=> $comment,
								'minRedLevel'		=> $level_l_min,
								'maxRedLevel'		=> $level_l_max,
								'minBlueLevel'		=> $level_r_min,
								'maxBlueLevel'		=> $level_r_max
							)
						);

						$this->db->insertAsDict(
							"game_battle_users",
							array
							(
								'BattleID' 		=> $this->db->lastInsertId(),
								'FighterID' 	=> $this->user->getId(),
								'Team' 			=> 0,
								'isBot' 		=> 0,
								'currentTime' 	=> time(),
								'TotalExpa' 	=> $this->getBaseLevelExp($this->user->level)
							)
						);
					}

					break;

				case 3:

					if ($this->user->level < 3)
						$message = 'Извините, хаотические бои с 3-ого уровня';
					else
					{
						$time_battle_start = $this->request->getPost('time_battle_start', 'int', 0);

						$alg = $this->request->getPost('alg', 'int', 1);
						$alg = min(1, max(0, $alg));

						$inv = $this->request->getPost('inv', 'int', 1);
						$inv = min(1, max(0, $inv));

						// Время до начала поединка
						if ($time_battle_start != 180 && $time_battle_start != 300 && $time_battle_start != 600 && $time_battle_start != 900)
							$time_battle_start = 180;

						// Уровни
						switch ($this->request->getPost('offer_level_1', 'int'))
						{
							case 2:
								$level_l_min = $this->user->level;
								$level_l_max = $this->user->level;
								break;
							case 3:
								$level_l_min = 0;
								$level_l_max = $this->user->level;
								break;
							case 4:
								$level_l_min = 0;
								$level_l_max = $this->user->level - 1;
								break;
							default:
								$level_l_min = 0;
								$level_l_max = 12;
						}

						$this->db->insertAsDict(
							"game_battle",
							array
							(
								'StartTime' 		=> time() + $time_battle_start,
								'BattleType'		=> 3,
								'RedTeamCapacity' 	=> 50,
								'BlueTeamCapacity' 	=> 0,
								'Timeout' 			=> $timeout,
								'Status' 			=> 'Zayavka',
								'Comment' 			=> $comment,
								'IsBlood' 			=> min(1, max(0, $this->request->getPost('blood', 'int', 0))),
								'minRedLevel'		=> $level_l_min,
								'maxRedLevel'		=> $level_l_max,
								'alg'				=> $alg,
								'inv'				=> $inv
							)
						);

						$this->db->insertAsDict(
							"game_battle_users",
							array
							(
								'BattleID' 		=> $this->db->lastInsertId(),
								'FighterID' 	=> $this->user->getId(),
								'Team' 			=> 0,
								'isBot' 		=> 0,
								'currentTime' 	=> time(),
								'TotalExpa' 	=> $this->getBaseLevelExp($this->user->level)
							)
						);
					}

					break;
			}
		}

		return $message;
	}

	private function takeOffer ($offerId)
	{
		$userOffer = $this->getCurrentUserRequest();

		$message = '';

		if (isset($userOffer['BattleID']))
			$message = "Для начала с одной заявкой разберись...";
		elseif ($this->user->hp_now < $this->user->hp_max / 3)
			$message = "Вы слишком ослаблены для поединка, подлечитесь!";
		else
		{
			$offerInfo = $this->db->query("SELECT * FROM `game_battle` WHERE `BattleID` = '" . $offerId . "'")->fetch();

			if ($offerInfo['BattleType'] == 1)
			{
				$participants = $this->db->query("SELECT `BattleID` FROM `game_battle_users` WHERE BattleID = " . $offerId . "")->numRows();

				switch ($participants)
				{
					case 1:

						$opponent = $this->db->query("SELECT f.`FighterID`, u.`username`, u.`room`, u.`ip` FROM `game_battle_users` f, game_users u WHERE f.BattleID = " . $offerId . " AND f.Team = 0 AND f.FighterID = u.id")->fetch();

						if ($opponent['ip'] == $this->user->ip && !$this->user->isAdmin())
							$message = "Вы не можете выступать против персонажа с таким же IP как у вас!";
						else
						{
							$this->db->insertAsDict(
								"game_battle_users",
								array
								(
									'BattleID' 		=> $offerId,
									'FighterID' 	=> $this->user->getId(),
									'Team' 			=> 1,
									'isBot' 		=> 0,
									'currentTime' 	=> time(),
									'TotalExpa' 	=> $this->getBaseLevelExp($this->user->level)
								)
							);

							$this->game->insertInChat("<b>" . $this->user->username . "</b> принял Вашу заявку!", $opponent['username'], true);
						}

						break;
					case 2:
						$message = "Кто-то оказался быстрее и перехватил заявку";
						break;
					default:
						$message = "Боец отозвал заявку или её не существует!";
				}
			}
			elseif ($offerInfo['BattleType'] == 2)
			{
				if ($this->user->level < 2)
					$message = 'Извините, групповые бои с 2-ого уровня';
				else
				{
					$side = min(1, max(0, $this->request->get('battle_side', 'int', 0)));

					$side_0 = $this->db->query("SELECT COUNT(*) AS num FROM `game_battle_users` WHERE `BattleID` = '" . $offerId . "' && `Team` = '0'")->fetch()['num'];
					$side_1 = $this->db->query("SELECT COUNT(*) AS num FROM `game_battle_users` WHERE `BattleID` = '" . $offerId . "' && `Team` = '1'")->fetch()['num'];

					if ($side_0 >= $offerInfo['RedTeamCapacity'] && $side == 0)
						$message = "<br>Группа уже набрана!";
					elseif ($side_1 >= $offerInfo['BlueTeamCapacity'] && $side == 1)
						$message = "<br>Группа уже набрана!";

					elseif ($this->user->level < $offerInfo['minRedLevel'] && $side == 0)
						$message = "<br>Эта заявка не может быть принята Вами!";
					elseif (($offerInfo['minRedLevel'] == $offerInfo['maxRedLevel']) && ($this->user->level != $offerInfo['minRedLevel']) && $side == 0)
						$message = "<br>Эта заявка не может быть принята Вами!";
					elseif ($this->user->level > $offerInfo['maxRedLevel'] && $side == 0)
						$message = "<br>Эта заявка не может быть принята Вами!";
					elseif ($this->user->level < $offerInfo['minBlueLevel'] && $side == 1)
						$message = "<br>Эта заявка не может быть принята Вами!";
					elseif (($offerInfo['minBlueLevel'] == $offerInfo['maxBlueLevel']) && ($this->user->level != $offerInfo['minBlueLevel']) && $side == 1)
						$message = "<br>Эта заявка не может быть принята Вами!";
					elseif ($this->user->level > $offerInfo['maxBlueLevel'] && $side == 1)
						$message = "<br>Эта заявка не может быть принята Вами!";
					else
					{
						$this->db->insertAsDict(
							"game_battle_users",
							array
							(
								'BattleID' 		=> $offerId,
								'FighterID' 	=> $this->user->getId(),
								'Team' 			=> $side,
								'isBot' 		=> 0,
								'currentTime' 	=> time(),
								'TotalExpa' 	=> $this->getBaseLevelExp($this->user->level)
							)
						);

						$this->db->query("UPDATE game_users SET `side` = '" . $side . "', `offer` = '" . $offerId . "' WHERE `id` = '" . $this->user->id . "'");
					}
				}
			}
			elseif ($offerInfo['BattleType'] == 3)
			{
				if ($this->user->level < 3)
					$message = 'Извините, хаотические бои с 3-ого уровня';
				else
				{
					if ($this->user->level < $offerInfo['minRedLevel'])
						$message = "<br>Эта заявка не может быть принята Вами!";
					elseif (($offerInfo['minRedLevel'] == $offerInfo['maxRedLevel']) && ($this->user->level != $offerInfo['minRedLevel']))
						$message = "<br>Эта заявка не может быть принята Вами!";
					elseif ($this->user->level > $offerInfo['maxRedLevel'])
						$message = "<br>Эта заявка не может быть принята Вами!";
					else
					{
						$this->db->insertAsDict(
							"game_battle_users",
							array
							(
								'BattleID' 		=> $offerId,
								'FighterID' 	=> $this->user->getId(),
								'Team' 			=> 0,
								'isBot' 		=> 0,
								'currentTime' 	=> time(),
								'TotalExpa' 	=> $this->getBaseLevelExp($this->user->level)
							)
						);

						$this->db->query("UPDATE `game_users` SET `side` = '0', `offer` = '" . $offerId . "' WHERE `id` = '" . $this->user->id . "'");
					}
				}
			}
		}

		return $message;
	}

	public function getBaseLevelExp ($level)
	{
		$level = $this->db->query("SELECT base FROM game_levels WHERE level = '" . intval($level) . "'")->fetch();

		if (isset($level['base']))
			return $level['base'];
		else
			return 0;
	}
}
?>