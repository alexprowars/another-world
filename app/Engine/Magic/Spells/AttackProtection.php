<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class AttackProtection extends AbstractSpell
{
	public function __construct(private readonly int $hours = 3)
	{
		parent::__construct(10);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		$this->requireOutsideBattle($caster, $target);

		if ($target->attack_protection?->isFuture()) {
			throw new Exception('На персонаже уже действует защита от нападения');
		}

		$target->attack_protection = now()->addHours($this->hours);
		$duration = $this->hours % 24 === 0
			? intdiv($this->hours, 24) . ' д.'
			: $this->hours . ' ч.';

		return $caster->name . ' наложил на персонажа ' . $target->name . ' защиту от нападения на ' . $duration;
	}
}
