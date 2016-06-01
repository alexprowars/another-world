<?

/**
 * @var array $iteminfo
 * @var $enemy \App\Models\Users
 * @var array $object
 * @var array $obj_inf
 */

$hp = 0;
$energy = 0;

switch ($iteminfo['name'])
{
	case 'addhp50':

		$hp = 50;
		$energy = 5;

		break;

	case 'addhp100':

		$hp = 100;
		$energy = 10;

		break;

	case 'addhp150':

		$hp = 150;
		$energy = 15;

		break;

	case 'addhp200':

		$hp = 200;
		$energy = 20;

		break;

	case 'addhp250':

		$hp = 250;
		$energy = 25;

		break;

	case 'addhp300':

		$hp = 300;
		$energy = 30;

		break;
}

if ($this->user->battle != $enemy->battle)
	$message = "Для использования нужно находиться в одном бою с персонажем!";
else
{
	if ($enemy->hp_now >= $enemy->hp_max)
		$message = "Персонаж '".$enemy->username."' не нуждается в востановлении!";
	elseif ($enemy->hp_now == 0 && $enemy->battle > 0)
		$message = "Мёртвому поможет только больница...";
	elseif ($this->user->energy_now < $energy)
		$message = "У вас не хватает маны!";
	else
	{
		if ($enemy->hp_now + $hp >= $enemy->hp_max)
			$hp = $enemy->hp_max - $enemy->hp_now;

		if ($this->user->id == $enemy->id)
		{
			$this->user->hp_now += $hp;
			$this->user->energy_now -= $energy;
		}

		$this->db->query("UPDATE game_users SET hp_now = hp_now + ".$hp." WHERE id = '".$enemy->id."'");
		$this->db->query("UPDATE game_users SET energy_now = energy_now - ".$energy." WHERE id = '".$this->user->id."'");

		$message = "Вы использовали ".$obj_inf[1]."...<br>Уровень жизни персонажа <u>".$enemy->username."</u> восстановлен на <u>+".$hp." ед.</u>";

		if ($this->user->id != $enemy->id)
			$MesgForAdd = "Персонаж <b><u>".$this->user->username."</u></b> восстановил Вам уровень жизни: <b><u>+".$hp." ед.</u></b>";
		else
			$MesgForAdd = "Вы использовали ".$obj_inf[1]."... Удачно восстановлен Ваш уровень жизни: <b><u>+".$hp." ед.</u></b>";

		$this->insertInChat($MesgForAdd, $enemy->username, true);
		$this->dropMagic($object['id']);
	}
}