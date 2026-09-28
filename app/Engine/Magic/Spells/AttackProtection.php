<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class AttackProtection extends AbstractSpell
{
	public function __construct()
	{
		parent::__construct(10);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		$this->requireOutsideBattle($caster, $target);

		if ($target->attack_protection_until?->isFuture()) {
			throw new Exception('На персонаже уже действует защита от нападения');
		}

		$target->attack_protection_until = now()->addHours(3);

		return $caster->name . ' наложил на персонажа ' . $target->name . ' защиту от нападения на 3 часа.';
	}
}
