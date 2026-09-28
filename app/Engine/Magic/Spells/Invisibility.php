<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class Invisibility extends AbstractSpell
{
	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->battle_id) {
			throw new Exception('Нельзя уйти в тень во время боя');
		}

		if (!$caster->is($target)) {
			throw new Exception('Это заклинание можно наложить только на себя');
		}

		if ($target->invisible?->isFuture()) {
			throw new Exception('Невидимость уже действует');
		}

		$target->invisible = now()->addHours(2);

		return 'Вы использовали «' . $item->title . '» и ушли в тень на два часа.';
	}
}
