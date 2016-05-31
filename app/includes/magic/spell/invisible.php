<?

/**
 * @var array $iteminfo
 * @var $enemy \App\Models\Users
 * @var array $object
 * @var array $obj_inf
 */

if ($this->user->battle > 0)
	$message = "Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
elseif ($this->user->id != $enemy->id)
	$message = "Данное заклинание можно наложить только на себя!";
elseif ($this->user->invisible > time())
	$message = "Данное заклинание было использовано ранее и ещё действует на Вас!";
else
{
	$this->db->query("UPDATE game_users SET invisible = ".(time() + 7200)." WHERE id = '" . $enemy->id . "'");

	if ($this->user->id == $enemy->id)
		$this->user->invisible += time() + 7200;

	$nms = "Всё прошло удачно...<br>Тень окутала Вас!";

	$MesgForAdd = "Вы использовали " . $obj_inf['1'] . " и удачно ушли в тень.</b>";

	$this->insertInChat($MesgForAdd, $enemy->username, true);
	$this->dropMagic($object['id']);
}

?>