<?

/**
 * @var array $iteminfo
 * @var $enemy \App\Models\Users
 * @var array $object
 * @var array $obj_inf
 */

$energy = 0;

switch ($iteminfo['name'])
{
	case 'addenergy25':

		$energy = 10;

		break;

	case 'addenergy50':

		$energy = 50;

		break;

	case 'addenergy100':

		$energy = 100;

		break;
}

$energyMax = $enemy['power'] * 5;

$items = $enemy->getSlot()->getItemsId();

if (count($items))
{
	$objectsEnergy = $this->db->query("SELECT SUM(energy) as energy FROM game_objects WHERE user_id = '".$enemy->id."' AND id IN (".implode(',', $items).") LIMIT 1")->fetch()['hp'];
	$energyMax += $objectsEnergy;
}

if ($enemy->energy_now >= $energyMax)
	$message = "Персонаж '" . $enemy->username . "' не нуждается в мане!";
else
{
	if ($enemy->energy_now + $energy >= $energyMax)
		$energy = $energyMax - $enemy->energy_now;

	if ($this->user->id == $enemy->id)
		$this->user->energy_now += $energy;

	$this->db->query("UPDATE game_users SET energy_now = energy_now + " . $energy . " WHERE id = '" . $enemy->id . "'");

	$message = "Вы использовали " . $obj_inf[1] . "...<br>Уровень энергии персонажа <u>" . $enemy->username . "</u> восстановлен на <u>+" . $energy . " ед.</u>";

	if ($this->user->id != $enemy->id)
		$MesgForAdd = "Персонаж <b><u>".$this->user->username."</u></b> восстановил Вам уровень маны: <b><u>+" . $energy . " ед.</u></b>";
	else
		$MesgForAdd = "Вы использовали " . $obj_inf[1] . "... Удачно восстановлен Ваш уровень маны: <b><u>+" . $energy . " ед.</u></b>";

	$this->insertInChat($MesgForAdd, $enemy->username, true);
	$this->dropMagic($object['id']);
}

?>