<?php

namespace App\Engine\Magic\Spells;

use App\Models\User;

class LightningBolt extends DamageSpell
{
	protected function damageAmount(User $caster, User $target): float
	{
		return $this->applyResistance($target, parent::damageAmount($caster, $target));
	}
}
