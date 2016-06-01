<?

/**
 * @var array $iteminfo
 * @var $enemy \App\Models\Users
 * @var array $object
 */

if ($this->user->battle > 0)
	$message = "Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
elseif ($this->user->id != $enemy->id)
	$message = "Данное заклинание можно наложить только на себя!";
else
{
	$this->db->query("UPDATE game_users SET energy_now = 0 WHERE id = '".$this->user->id."'");

	$enemy->getSlot()->unsetObject();

	$updates = $this->db->query("SELECT SUM(updates) as updates FROM game_levels WHERE exp <= ".$enemy['exp']."")->fetc()['updates'];

	$this->db->query("UPDATE game_users SET strength = 3, dex = 3, agility = 3, vitality = 3, power = 1, razum = 0, battery = 1, s_updates = ".$updates.", hp_now = 15, energy_now = 5 WHERE id = '".$enemy->id."'");

	$this->user->strength = 3;
	$this->user->dex = 3;
	$this->user->agility = 3;
	$this->user->vitality = 3;
	$this->user->power = 1;
	$this->user->razum = 0;
	$this->user->battery = 1;
	$this->user->duh = 0;
	$this->user->hp_now = 15;
	$this->user->energy_now = 15;
	$this->user->hp = 0;
	$this->user->energy = 0;
	$this->user->br1 = 0;
	$this->user->br2 = 0;
	$this->user->br3 = 0;
	$this->user->br4 = 0;
	$this->user->br5 = 0;
	$this->user->krit = 0;
	$this->user->unkrit = 0;
	$this->user->uv = 0;
	$this->user->unuv = 0;
	$this->user->min = 0;
	$this->user->max = 0;

	$message = "Всё прошло удачно...<br>Ваши параметры сброшены!";

	$this->dropMagic($object['id']);
}