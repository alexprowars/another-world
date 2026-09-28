<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\Effect;
use App\Models\User;
use App\Models\UserItem;

class CleanseAura extends AbstractSpell
{
	public function cast(User $caster, User $target, UserItem $item): string
	{
		$this->requireSelf($caster, $target);

		if (!$target->effects()->where('type', Effect::AURA)->whereFuture('date')->exists()) {
			throw new Exception('На персонаже нет действующей ауры');
		}

		$target->effects()->where('type', Effect::AURA)->delete();

		return $target->name . ' очистился от действия аур.';
	}
}
