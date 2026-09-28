<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;

class MagicHand extends DamageSpell
{
	public function __construct()
	{
		parent::__construct(0, 0);
	}

	protected function damageAmount(User $caster, User $target): float
	{
		if ($caster->energy_now <= 0) {
			throw new Exception('У вас нет маны');
		}

		$damage = $this->applyResistance($target, $caster->energy_now * 2);
		$caster->energy_now = 0;

		return $damage;
	}
}
