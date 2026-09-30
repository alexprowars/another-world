<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class BattleFury extends AbstractSpell
{
	public function cast(User $caster, User $target, UserItem $item): string
	{
		$this->requireOutsideBattle($caster, $target);

		if ($target->battle_fury?->isFuture()) {
			throw new Exception('На персонаже уже действует Боевая ярость');
		}

		$target->battle_fury = now()->addWeek();

		return $caster->name . ' наложил на персонажа ' . $target->name
			. ' Боевую ярость на одну неделю. Опыт в боях увеличен в 2 раза.';
	}
}
