<?

/**
 * @var array $iteminfo
 * @var $enemy \App\Models\Users
 * @var array $object
 */

$damage = 0;
$energy = 0;

switch ($iteminfo['name'])
{
	case 'showstorm20':

		$damage = 20;
		$energy = 5;

		break;

	case 'showstorm30':

		$damage = 30;
		$energy = 10;

		break;

	case 'showstorm40':

		$damage = 40;
		$energy = 15;

		break;
}


if ($this->user->battle != $enemy->battle || $this->user->battle == 0)
	$message = "Для использования нужно находиться в одном бою с персонажем!";
elseif ($this->user->id == $enemy->id)
	$message = "Вы не можете атаковать сами себя!";
elseif ($enemy->hp_now == 0 && $enemy->battle > 0)
	$message = "Извините но это не ЖИВАЯ ВОДА, мертвому она никчему";
elseif ($this->user->energy_now <= $energy)
	$message = "У вас не хватает маны!";
else
{
	// Расчитываем силу удара свитка
	$damage += mt_rand(round($this->user->razum / 1.5), round(1 + $this->user->razum)) + rand(0, 5);

	if ($enemy->hp_now - $damage <= 0)
		$damage = $enemy->hp_now;

	if ($this->user->id != $enemy->id)
		$MesgForAdd = "Маг <b><u>".$this->user->username."</u></b> использовал против вас ледяной шторм... Холод поглотил ваши жизни <b><u>-".$damage." НР<u></b>";
	else
		$MesgForAdd = "Наверное вы  недостаточно  обучены магии... Вы прочитали свиток и замёрзли  на ... : <b><u>-".$damage." НР</u></b>";

   	$this->insertInChat($MesgForAdd, $enemy->username, true);

	$this->db->query("UPDATE game_users SET hp_now = ".$damage." WHERE id = '".$enemy->id."'");
	$this->db->query("UPDATE game_users SET energy_now = energy_now - ".$energy." WHERE id = '".$this->user->id."'");

	$message = "Свиток ледяного шторма прочитан ...<br>Персонажа <u>".$enemy->username."</u> ударила волна холода и он замёрз на <u>-".$damage." HP</u>";

	// Если перс в бою, то обновляем таблицы боя
	if ($this->user->battle > 0)
		$this->db->query("UPDATE `game_battle_users` SET `damage` = damage + $damage WHERE `BattleID` = '".$this->user->battle."' AND `FighterID` = '".$this->user->id."'");

	$this->dropMagic($object['id']);
}