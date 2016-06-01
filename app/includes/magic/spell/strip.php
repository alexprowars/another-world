<?

/**
 * @var $iteminfo array
 * @var $enemy \App\Models\Users
 * @var $object array
 */

if ($this->user->energy_now < 20)
	$message = "У вас не хватает маны!";
else
{
	$enemy->getSlot()->unsetObject();

	$this->db->query("UPDATE game_users SET energy_now = energy_now - 20 WHERE id = '".$this->user->id."'");

	if ($this->user->id != $enemy->id)
		$MesgForAdd = "Вас раздел маг <b><u>".$this->user->username."</u></b>";
	else
		$MesgForAdd = "Вы раздели сами себя. Теперь сражайтесь голым!!!";

	$this->insertInChat($MesgForAdd, $enemy->username, true);

	$message = "Свиток магии прочитан. Персонаж <b><u>".$enemy->username."</u></b> теперь голый!";

	$this->dropMagic($object['id']);
}