<?

/**
 * @var $this App\Game\Controllers\ControllerBase
 */

use App\Models\Objects;

$message = '';

if ($this->request->hasQuery('repair'))
{
	$id = $this->request->getQuery('repair', 'int');

	if (!is_numeric($id) || $id <= 0)
		$message = 'Ошибка';
	else
	{
		$nowobject = $this->db->query("SELECT * FROM game_objects WHERE id = '" . $id . "' AND user_id = '" . $this->user->getId() . "'")->fetch();

		$info = explode("|", $nowobject['inf']);
		$min  = explode("|", $nowobject['min']);

		if ($this->request->hasQuery('full') && $this->request->getQuery('full') == 'Y')
			$stoimost = 0.05 * ($min[0] * $info[6]);
		else
			$stoimost = 0.05 * $min[0];

		if ($info[5] == 1)
			$stoimost *= 10;

		if ($nowobject['tip'] == 17 || !$nowobject['inf'])
			$message = "Ремонту не подлежит!";
		else
		{
			if ($this->user->credits >= $stoimost)
			{
				$info[6] = ($this->request->hasQuery('full') && $this->request->getQuery('full') == 'Y') ? 0 : ($info[6] - 1);

				$this->db->query("UPDATE game_users SET credits = credits - ".$stoimost." WHERE id = " . $this->user->getId() . "");
				$this->db->query("UPDATE game_objects SET inf = '" . $info[0] . "|" . $info[1] . "|" . $info[2] . "|" . $info[3] . "|" . $info[4] . "|" . $info[5] . "|".$info[6]."|" . $info[7] . "' WHERE id = '" . $nowobject['id'] . "'");

				$message = "Предмет починен!";
			}
			else
				$message = "Недостаточно кредитов!";
		}
	}
}

if ($this->request->hasQuery('cut'))
{
	$id = $this->request->getQuery('cut', 'int');

	if (!is_numeric($id) || $id <= 0)
		$message = 'Ошибка';
	else
	{
		$instrument = $this->db->query("SELECT * FROM game_objects WHERE user_id = '" . $this->user->id . "' AND tip = 18 AND min = '1|0|0|0|0|0|0|3' AND onset = 3")->fetch();

		if (isset($instrument['id']))
		{
			$nowobject = $this->db->query("SELECT * FROM game_objects WHERE id = '" . $id . "' AND user_id = '" . $this->user->id . "'")->fetch();

			$info = explode("|", $nowobject['inf']);
			$min = explode("|", $nowobject['min']);

			if ($this->user->proff == 3)
			{
				if ($this->user->ustal_now >= 15)
				{
					if ($this->user->r_time == 0)
					{
						$item = $this->db->query("SELECT * FROM game_items WHERE name = '" . $info[0] . "'")->fetch();

						$this->db->query("UPDATE game_users set ustal_now = ustal_now - 15, r_time = ".time()." + 600, r_type = 7 WHERE id = ".$this->user->id."");
						$this->db->query("UPDATE game_objects SET inf = '" . $info['0'] . "|" . $info['1'] . "|30|" . $info['3'] . "|" . $info['4'] . "|" . $info['5'] . "|0|" . $info['7'] . "', tip=20, hp='" . $item['hp'] . "',energy='" . $item['energy'] . "',razum='" . $item['razum'] . "',min_d='" . $item['min'] . "',max_d='" . $item['max'] . "',strength='" . $item['strength'] . "',dex='" . $item['dex'] . "',agility='" . $item['agility'] . "',vitality='" . $item['vitality'] . "',krit='" . $item['krit'] . "',unkrit='" . $item['unkrit'] . "',uv='" . $item['uv'] . "',unuv='" . $item['unuv'] . "',time='" . time() . "',about='Может быть вставлен в предметы для изменения характеристик' WHERE id='" . $nowobject['id'] . "'");

						$instr_inf = explode("|", $instrument['inf']);
						$iznos = ($instr_inf[6] + 1);

						if ($instr_inf[7] > $iznos)
							$this->db->query("UPDATE game_objects SET inf='" . $instr_inf['0'] . "|" . $instr_inf['1'] . "|" . $instr_inf['2'] . "|" . $instr_inf['3'] . "|" . $instr_inf['4'] . "|" . $instr_inf['5'] . "|" . $iznos . "|" . $instr_inf['7'] . "' WHERE id='" . $instrument['id'] . "'");
						else
						{
							$this->db->query("UPDATE game_objects SET inf='" . $instr_inf['0'] . "|" . $instr_inf['1'] . "|" . $instr_inf['2'] . "|" . $instr_inf['3'] . "|" . $instr_inf['4'] . "|" . $instr_inf['5'] . "|" . $iznos . "|" . $instr_inf['7'] . "' WHERE id='" . $instrument['id'] . "'");
							$this->db->query("UPDATE game_slots set slots.i3=0 WHERE user_id = " . $this->user->id . "");
						}

						$this->user->r_time = time() + 600;
						$this->user->r_type = 7;

						$message = "Процесс начат!";
					}
					else
						$message = "Вы заняты другой работой!";
				}
				else
					$message = "Вы слишком устали для этого дела!";
			}
			else
				$message = "Огранкой может заниматся только Огранщик!";
		}
		else
			$message = "Вы не можете делать огранку без спец инструмента!";
	}
}

if ($this->request->hasPost('etching'))
{
	$text 	= addslashes($this->request->getPost('text'));
	$item 	= intval($this->request->getPost('etching', 'int'));

	if (!empty($text))
	{
		$items = $this->user->getSlot()->getItemsId();

		$object = $this->db->query("SELECT id, inf FROM game_objects WHERE user_id = '" . $this->user->id . "' AND id = " . $item . " AND tip < 12 ".(count($items) ? 'AND id NOT IN ('.implode(',', $items).')' : ''))->fetch();

		if (isset($object['id']))
		{
			if (preg_match("/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\ ]+$/iu", $text))
			{
				if ($this->user->credits >= 150)
				{
					if (mb_strlen($text, 'UTF-8') <= 25)
					{
						$inf = explode("|", $object['inf']);

						if ($inf[3] == 0)
						{
							$infs = $inf[0] . "|" . $inf[1] . "|" . $inf[2] . "|" . $text . "|" . $inf[4] . "|" . $inf[5] . "|" . $inf[6] . "|" . $inf[7];

							$this->db->query("UPDATE game_objects SET inf = '" . $infs . "' WHERE id = " . $object['id'] . "");
							$this->db->query("UPDATE game_users SET credits = credits - 150 WHERE id = '" . $this->user->id . "'");

							$message = "Вы удачно выгравировали надпись <U>" . $text . "</U> на предмете <U>" . $inf[1] . "</U>, заплатив при этом 150 золота";
						}
						else
							$message = "На этом предмете уже есть гравировка!";
					}
					else
						$message = "Текст гравировки не должен быть длинее 25 символов!";
				}
				else
					$message = "Недостаточно кредитов!";
			}
			else
				$message = "В тексте гравировки можно указывать только русские или английские буквы!";
		}
		else
			$message = "Что-то тут не так..";
	}
	else
		$message = "Введите текст гравировки!";
}


if ($this->request->hasQuery('upgrade'))
{
	if ($this->user->proff == 2)
	{
		if ($this->user->f_credits >= 50)
		{
			$objectId = (int) $this->request->getQuery('upgrade', 'int');

			$object = $this->db->query("SELECT * FROM `game_objects` WHERE id = '" . $objectId . "' AND `user_id` = '" . $this->user->id . "'")->fetch();

			if (isset($object['id']))
			{
				$object['min_d'] += mt_rand(1, 1);
				$object['max_d'] += mt_rand(1, 1);

				$obj_inf = explode("|", $object['inf']);


				if ($obj_inf['7'] - 20 > 0)
				{
					$obj_inf['7'] -= 20;
					$inf = $obj_inf['0'] . "|" . $obj_inf['1'] . "|" . $obj_inf['2'] . "|" . $obj_inf['3'] . "|" . $obj_inf['4'] . "|" . $obj_inf['5'] . "|" . $obj_inf['6'] . "|" . $obj_inf['7'] . "";

					$this->db->query("UPDATE `game_objects` SET `inf` = '".$inf."', `min_d` = '".$object['min_d']."', `max_d` = '".$object['max_d']."' WHERE id='" . $object['id'] . "' ");
					$this->db->query("UPDATE `game_users` SET `f_credits` = `f_credits` - 50 WHERE `id` = '".$this->user->id."' ");

					$message = "Модернизация прошла успешно";

					$this->game->insertInChat("Модернизация <b>" . $obj_inf['1'] . "</b>  прошла успешно. Минимальный урон стал ".$object['min_d'].", а максимальный ".$object['max_d']." . Долговечность предмета уменьшилась на 20 ед.", "", "", "1", $stat['user'], "", $stat['room']);
				}
				else
					$message = "Ваш предмет слишком не прочен";
			}
			else
				$message = 'Вещь не найдена в вашем рюкзаке';
		}
		else
			$message = "У вас недостаточно денег";
	}
	else
		$message = "Модернизировать может только Кузнец!";
}




if ($this->request->hasQuery('insert'))
{
	$objectId = (int) $this->request->getQuery('insert', 'int');
	$weaponId = (int) $this->request->getQuery('weapon', 'int');

	if ($objectId > 0 && $weaponId > 0)
	{
		$instrument = $this->db->query("SELECT * FROM game_objects WHERE user_id = '" . $this->user->id . "' AND tip = 18 AND min = '1|0|0|0|0|0|0|2' AND onset = 3")->fetch();

		if (isset($instrument['id']))
		{
			$nowobject = $this->db->query("SELECT * FROM game_objects WHERE id = '" . $objectId . "' AND user_id = '" . $this->user->id . "'")->fetch();

			$info = explode("|", $nowobject['inf']);

			$weapon = $this->db->query("SELECT * FROM game_objects WHERE id = '" . $weaponId . "' AND user_id = '" . $this->user->id . "'")->fetch();

			if (isset($weapon['id']) && $weapon['mf_type'] == 0)
			{
				$weap_inf = explode("|", $weapon['inf']);

				$weap_inf[2] += 20;
				$weap_inf[1] = $weap_inf[1].'[МФ]';

				$weapon['strength'] += $nowobject['strength'];
				$weapon['agility'] 	+= $nowobject['agility'];
				$weapon['dex'] 		+= $nowobject['dex'];
				$weapon['vitality'] += $nowobject['vitality'];
				$weapon['krit'] 	+= $nowobject['krit'];
				$weapon['unkrit'] 	+= $nowobject['unkrit'];
				$weapon['uv'] 		+= $nowobject['uv'];
				$weapon['unuv'] 	+= $nowobject['unuv'];

				if ($this->user->proff == 2)
				{
					if ($this->user->ustal_now >= 15)
					{
						if ($this->user->r_time == 0)
						{
							$instr_inf = explode("|", $instrument['inf']);
							$iznos = ($instr_inf[6] + 1);

							if ($instr_inf[7] > $iznos)
								$this->db->query("UPDATE game_objects SET inf = '" . $instr_inf['0'] . "|" . $instr_inf['1'] . "|" . $instr_inf['2'] . "|" . $instr_inf['3'] . "|" . $instr_inf['4'] . "|" . $instr_inf['5'] . "|" . $iznos . "|" . $instr_inf['7'] . "' WHERE id = '" . $instrument['id'] . "'");
							else
							{
								$this->db->query("UPDATE game_objects SET inf='" . $instr_inf['0'] . "|" . $instr_inf['1'] . "|" . $instr_inf['2'] . "|" . $instr_inf['3'] . "|" . $instr_inf['4'] . "|" . $instr_inf['5'] . "|" . $iznos . "|" . $instr_inf['7'] . "' WHERE id = '" . $instrument['id'] . "'");
								$this->db->query("UPDATE slots set slots.i3=0 WHERE user_id = " . $this->user->id . "");
							}

							$this->db->query("UPDATE game_users SET ustal_now = ustal_now - 15, r_time = ".time()." + 600, r_type = 7 WHERE id= ".$this->user->id."");
							$this->db->query("UPDATE game_objects SET inf='" . $weap_inf['0'] . "|" . $weap_inf[1] . "|" . $weap_inf[2] . "|" . $weap_inf['3'] . "|" . $weap_inf['4'] . "|" . $weap_inf['5'] . "|" . $weap_inf[6] . "|" . $weap_inf['7'] . "', strength='" . $weapon['strength'] . "',dex='" . $weapon['dex'] . "',agility='" . $weapon['agility'] . "',vitality='" . $weapon['vitality'] . "',krit='" . $weapon['krit'] . "',unkrit='" . $weapon['unkrit'] . "',uv='" . $weapon['uv'] . "',unuv='" . $weapon['unuv'] . "',time='" . time() . "',mf_type=1 WHERE id='" . $weapon['id'] . "'");
							$this->db->query("DELETE FROM game_objects WHERE `id` = " . $nowobject['id'] . "");

							$this->user->r_time = time() + 600;
							$this->user->r_type = 7;

							$message = "Процесс начат!";
						}
						else
							$message = "Вы заняты другой работой!";
					}
					else
						$message = "Вы слишком устали для этого дела!";
				}
				else
					$message = "Это работа только для Кузнеца!";
			}
			else
				$message = "Эта вещь уже модернизирована!";
		}
		else
			$message = "Вы не можете делать вставку камня без спец инструмента!";
	}
}


if ($this->user->r_type == 7 && $this->user->r_time - 2 < time())
{
	$this->db->query("UPDATE `game_users` SET r_time = 0, r_type = 0 WHERE id = '" . $this->user->id . "'");

	$this->user->r_time = 0;
	$this->user->r_type = 0;
}

$otdel = $this->request->get('otdel', 'int');

switch ($otdel)
{
	case 2:

		if ($this->user->r_time)
			$message = 'Вы заняты какой-то работой';
		elseif ($this->user->proff != 3)
			$message = 'Только Огранщик может заниматься огранкой камней!';
		else
		{
			$objects = Objects::query()->where('user_id = ' . $this->user->id . ' AND tip = 20 AND (strength = 0 AND dex = 0 AND agility = 0 AND vitality = 0 AND krit = 0 AND unkrit = 0 AND uv = 0 AND unuv = 0)')->orderBy('time DESC')->execute();

			$this->view->setVar('objects', $objects);

			if (count($objects) == 0)
				$message = "У Вас нет камней, подлежащих огранке.";
		}

		break;


	case 3:

		if ($this->user->r_time)
			$message = 'Вы заняты какой-то работой';
		else
		{
			$items = $this->user->getSlot()->getItemsId();

			$objects = Objects::query()->where('user_id = ' . $this->user->id . ' AND ((tip >= 1 AND tip <= 11) OR (tip >= 24 AND tip <= 25)) AND present=0 AND bank=0 AND sclad=0 AND komis=0 AND '.(count($items) ? 'id NOT IN ('.implode(',', $items).')' : '').'')->orderBy('time DESC')->execute();

			$this->view->setVar('objects', $objects);

			if (count($objects) == 0)
				$message = "У Вас нет предметов, подлежащих гравировке";
		}

		break;

	case 4:

		if ($this->user->r_time)
			$message = 'Вы заняты какой-то работой';
		elseif ($this->user->proff != 2)
			$message = 'Только Кузнец может заниматься кузнечным делом!';
		else
		{
			$objects = Objects::query()->where('user_id = ' . $this->user->id . ' AND tip = 20')->orderBy('time DESC')->execute();

			$weapons = Objects::query()->where('user_id = ' . $this->user->id . ' AND ((tip >= 1 AND tip <= 11) OR (tip >= 24 AND tip <= 25)) AND mf_type = 0')->orderBy('time DESC')->execute();

			$this->view->setVar('objects', $objects);
			$this->view->setVar('weapons', $weapons);

			if (count($objects) == 0)
				$message = "У Вас нет драгоценных камней, подлежащих вставке.";
		}

		break;

	default:

		if (!$this->user->r_time)
		{
			$objects = Objects::query()->where('user_id = ' . $this->user->id . ' AND ((tip >= 1 AND tip <= 11) OR (tip >= 24 AND tip <= 25) OR tip = 18) AND present=0 AND bank=0 AND sclad=0 AND komis=0')->orderBy('time DESC')->execute();

			$this->view->setVar('objects', $objects);

			if (count($objects) == 0)
				$message = "У Вас нет предметов, подлежащих починке.";
		}
		else
			$message = 'Вы заняты какой-то работой';
}

$this->view->pick('shared/city/1_repair');
$this->view->setVar('message', $message);
$this->view->setVar('otdel', $otdel);

?>