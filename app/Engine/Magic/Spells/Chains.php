<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class Chains extends AbstractSpell
{
	public function isOffensive(): bool
	{
		return true;
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->is($target)) {
			throw new Exception('Нельзя донести на себя');
		}

		if ($caster->battle_id || $target->battle_id) {
			throw new Exception('Нельзя использовать донос во время боя');
		}

		if ($target->prison?->isFuture()) {
			throw new Exception('Персонаж уже находится в тюрьме');
		}

		$target->prison = now()->addMinutes(15);
		$target->prison_reason = 'Донос на персонажа';
		$target->location = $target->currentLocation()->inCity('prison')->value();

		if ($target->r_type == 10) {
			$target->vault_destination_id = null;
			$target->r_date = null;
			$target->r_type = null;
		}

		return 'На персонажа ' . $target->name . ' поступил донос. Он отправлен в тюрьму на 15 минут.';
	}
}
