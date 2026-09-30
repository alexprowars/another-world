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
		$target->room = 666;

		return 'На персонажа ' . $target->name . ' поступил донос. Он отправлен в тюрьму на 15 минут.';
	}
}
