<?php
namespace App\Models;

use Phalcon\Mvc\Model;

class Complects extends Model
{
	public $id;
	public $user_id;
	public $name;
	public $data;

	public function getSource()
	{
	 	return DB_PREFIX."complects";
	}

	public function getData ()
	{
		return json_decode($this->data, true);
	}

	public function wear ()
	{
		/**
		 * @var $db \Phalcon\Db\Adapter\Pdo\Mysql
		 */
		$db = $this->getDI()->getShared('db');
		/**
		 * @var $user \App\Models\Users
		 */
		$user = $this->getDI()->getShared('user');

		$data = $this->getData();

		$update = array();

		for ($i = 1; $i <= 22; $i++)
		{
			if ($data['i'.$i] != 0)
			{
				$sel = $db->query("SELECT `id`, `inf`, `min`, tip FROM game_objects WHERE id = ".$data['i'.$i]." AND bank = 0 AND komis = 0 AND sclad = 0");

				if ($sel->numRows() == 0)
					$data['i'.$i] = 0;
				else
				{
					$objects = $sel->fetch();
					$obj_inf = explode("|", $objects['inf']);
					$obj_min = explode("|", $objects['min']);

					if (($user->level < $obj_min[0] || $user->strength < $obj_min[1] || $user->dex < $obj_min[2] ||$user->agility < $obj_min[3] ||$user->vitality < $obj_min[4] ||$user->razum < $obj_min[5] || ($obj_min[7] != 0 &&$user->proff != $obj_min[7])) || $objects['tip'] == 13 || $obj_inf[6] >= $obj_inf[7])
						$data['i'.$i] = 0;
					else
						$db->query("UPDATE game_objects SET onset = '".$i."' WHERE id = " . $objects['id'] . " ");
				}
			}
			else
				$data['i'.$i] = 0;

			$update['i'.$i] = $data['i'.$i];
		}

		$db->updateAsDict(
		   	"game_slots",
			$update,
		   	"user_id = ".$user->id
		);

		return true;
	}
}

?>