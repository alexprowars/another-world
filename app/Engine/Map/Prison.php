<?php

namespace App\Engine\Map;

class Prison
{
	public function __invoke()
	{
		$user = auth()->user();

		if ($user->t_time < time()) {
			$this->db->query("UPDATE game_users SET t_time=0 WHERE id = ".$this->user->id."");

			$this->user->t_time = 0;
		}
	}
}
