<?

/**
 * @var \Game\Controllers\MapController $this
 */


$max_attack = 15;

if ($this->user->r_type == 10)
{
	if ($this->user->r_time - 2 < time())
	{
		$this->db->query("UPDATE game_users SET room = v_room, r_time = '0', r_type = '0' WHERE id = '" . $this->user->id . "'");

		$this->user->r_time = 0;
		$this->user->room = $this->user->v_room;
		$this->user->r_type = 0;
		$tmp = 1;
	}
}

$message = '';

$VaultInfo = $this->db->query("SELECT * FROM game_vault WHERE id = '" . $this->user->room . "'")->fetch();

if ($this->request->hasQuery('heal'))
{
	if ($this->user->r_type != 0)
		$message = "Вы не можете лечиться во время работы!";
	elseif ($this->user->room == 200)
		$message = "Здесь колодец пустой!";
	elseif ($this->user->room < 200 && $this->user->room > 370)
		$message = "Ошипко!!!! Багоюзер!!!!!";
	else
	{
		if ($VaultInfo['heal'] >= time())
			$message = "Кто-то оказался быстрее и выпил всю энергию из Колодца Жизни!";
		else
		{
			if ($this->user->hp_now < $this->user->hp_max)
			{
				$VaultInfo['heal'] = time() + 3600;
				$this->db->query("UPDATE game_vault SET heal = '" . $VaultInfo['heal'] . "' WHERE id = '" . $VaultInfo['id'] . "'");
				$this->db->query("UPDATE game_users SET hp_now = '" . $this->user->hp_max . "' WHERE id = '" . $this->user->id . "'");
				$this->user->hp_now = $this->user->hp_max;
				$message = "Ваш уровень жизни полностью восстановлен!";
			}
			else
				$message = "Вы не нуждаетесь в лечении!";
		}
	}
}

if ($this->request->hasPost('dig') && $this->request->hasPost('captcha'))
{
	$girlen = $this->request->getPost('captcha');

	if ($_SESSION['captcha'] == $girlen)
	{
		$instrument = $this->db->query("SELECT o.* FROM game_objects o, game_slots s WHERE o.user_id = '" . $this->user->getId() . "' AND s.user_id = o.user_id AND o.inf LIKE 'kirka|%' AND o.tip = 18 AND o.id IN (s.i3)")->fetch();

		if (isset($instrument['id']))
		{
			if ($this->user->ustal_now >= 15)
			{
				if ($this->user->r_type == 0)
				{
					$instr_inf = explode("|", $instrument['inf']);
					$instr_inf[6]++;

					$this->db->query("UPDATE game_objects SET inf = '" . $instr_inf['0'] . "|" . $instr_inf['1'] . "|" . $instr_inf['2'] . "|" . $instr_inf['3'] . "|" . $instr_inf['4'] . "|" . $instr_inf['5'] . "|" . $instr_inf[6] . "|" . $instr_inf['7'] . "' WHERE id = '" . $instrument['id'] . "'");

					if ($instr_inf[7] <= $instr_inf[6])
						$this->db->query("UPDATE game_slots set i3 = 0 WHERE user_id = " . $this->user->getId() . "");

					// Если профа шахтёра то на время добычи на 25% меньше
					if ($this->user->proff == 5)
						$dtime = 1125;
					else
						$dtime = 1500;

					$this->db->query("UPDATE game_users SET r_time = ".(time() + $dtime).", r_type = 8, ustal_now = ustal_now - 15 WHERE id = '" . $this->user->getId() . "'");

					$this->user->r_time = time() + $dtime;
					$this->user->r_type = 8;

					$message = "Добыча руды началась.";
				}
				else
					$message = "Вы уже работаете!";
			}
			else
				$message = "Да вы батенька заработались! Идите-ка посражайтесь.";
		}
		else
			$message = "Без кирки добывать руду нельзя!";
	}
	else
		$message = "Неправильный ввод цифр.";
}

if ($this->request->hasQuery('unwork'))
{
	if ($this->user->r_time != 0 && $this->user->r_type == 8)
	{
		$this->db->query("UPDATE game_users SET r_time = '0', r_type = '0' WHERE id = '" . $this->user->getId() . "'");

		$this->user->r_time = 0;
		$this->user->r_type = 0;

		$message = "Вы успешно отменили добычу руды.";
	}
	else
		$message = "Вы не заняты никакой работой в шахте!";
}

$GoIn = $this->request->getQuery('GoIn', null, '');

// Переход
if ($GoIn != '' && ($GoIn == "top" || $GoIn == "bottom" || $GoIn == "left" || $GoIn == "right") && !$tmp)
{
	if ($this->user->r_type != 0)
		$message = "Вы заняты какой то работой!";
	else
	{
		$GoInfo = $this->db->query("SELECT * FROM game_vault WHERE id = '" . $VaultInfo[$GoIn . '_id'] . "'")->fetch();

		if ($GoInfo['id'])
		{
			$this->user->r_time = time() + $GoInfo['time'];
			$this->user->v_room = $GoInfo['id'];
			$this->user->r_type = 10;

			$this->db->query("UPDATE game_users SET v_room = '" . $GoInfo['id'] . "', r_time = '" . $this->user->r_time . "', r_type = '10' WHERE id = '" . $this->user->id . "'");

			$GoToText = "Топаем в <b><u>" . $GoInfo['title'] . "</u></b>";
		}
	}
}

if ($this->user->r_type == 8)
{
	if ($this->user->r_time - 2 < time())
	{
		$this->db->query("UPDATE `game_users` SET `r_time` = '0', `r_type` = '0' WHERE `id` = '" . $this->user->getId() . "'");

		$this->user->r_time = 0;
		$this->user->r_type = 0;

		// С профой рабочего шанс добыть драг. камень выше
		if ($this->user->proff == 5)
			$res = mt_rand(2, 7);
		else
			$res = mt_rand(0, 9);

		if ($res == 5)
		{
			$resurs = array();
			$resurs[0] = "alexandrit|Александрит";
			$resurs[1] = "almaz|Алмаз";
			$resurs[2] = "amazonit|Амазонит";
			$resurs[3] = "biruza|Бирюза";
			$resurs[4] = "pirit|Пирит";
			$resurs[5] = "opal|Опал";
			$resurs[6] = "rubin|Рубин";
			$resurs[7] = "sapfir|Сапфир";

			$res_type = $resurs[array_rand($resurs)];

			$this->db->query("INSERT INTO `game_objects` (`user_id`, `inf`, `min`, `tip`, `time`, `about`) VALUES ('" . $this->user->getId() . "','" . $res_type . "|15.00|0|0|0|0|1','0|0|0|0|0|0|0|0','20','" . time() . "', 'Неограненный камень')");

			$this->game->insertInChat("Поздравляем! Вы добыли драгоценный камень в кол-ве <b><u>1 ед</u></b>!", $this->user->username);
		}
		else
		{
			$this->db->query("INSERT INTO `game_objects` (`user_id`, `inf`, `min`, `tip`, `time`, `about`) VALUES ('" . $this->user->getId() . "','ruda|Руда|6.00|0|0|0|0|1','0|0|0|0|0|0|0|0','19','" . time() . "', 'Руда')");

			$this->game->insertInChat("Вы добыли руду в кол-ве <b><u>1 ед</u></b>!", $this->user->username);
		}
	}
}

$this->view->pick('shared/city/1_vault');
$this->view->setVar('message', $message);
$this->view->setVar('vault', $VaultInfo);