<?php

namespace App\Engine\Magic\Spells;

use App\Models\User;
use App\Models\UserItem;

class EnergyPotion extends Potion
{
	protected function validateTarget(User $caster, User $target): void
	{
		parent::validateTarget($caster, $target);
		$this->requireOutsideBattle($caster, $target);
	}

	protected function applyPotion(User $caster, User $target, UserItem $item): string
	{
		return (new RestoreEnergy(200))->cast($caster, $target, $item);
	}
}
