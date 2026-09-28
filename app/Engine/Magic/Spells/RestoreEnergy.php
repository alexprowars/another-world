<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class RestoreEnergy extends AbstractSpell
{
	public function __construct(private readonly int $amount)
	{
		parent::__construct();
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		$energy = min($this->amount, $target->energy_max - $target->energy_now);

		if ($energy <= 0) {
			throw new Exception('Персонаж не нуждается в восстановлении маны');
		}

		$target->energy_now += $energy;

		return $caster->name . ' использовал «' . $item->title . '». Мана персонажа ' . $target->name . ' восстановлена на ' . $energy . ' MP.';
	}
}
