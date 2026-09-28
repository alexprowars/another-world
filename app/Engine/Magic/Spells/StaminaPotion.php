<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class StaminaPotion extends Potion
{
	protected function validateTarget(User $caster, User $target): void
	{
		$this->requireOutsideBattle($caster, $target);
	}

	protected function applyPotion(User $caster, User $target, UserItem $item): string
	{
		$amount = $target->stamina_max - $target->stamina_now;

		if ($amount <= 0) {
			throw new Exception('Персонаж не нуждается в восстановлении активности');
		}

		$target->stamina_now = $target->stamina_max;

		return $caster->name . ' восстановил активность персонажа ' . $target->name . ' на ' . $amount . ' ед.';
	}
}
