<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class VampireProtection extends AbstractSpell
{
	public function __construct(private readonly int $hours)
	{
		parent::__construct();
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		$this->requireOutsideBattle($caster, $target);

		if ($target->vampire_protection_until?->isFuture()) {
			throw new Exception('На персонаже уже действует защита от вампиров');
		}

		$target->vampire_protection_until = now()->addHours($this->hours);

		return $caster->name . ' наложил на персонажа ' . $target->name . ' защиту от вампиров на ' . $this->hours . ' ч.';
	}
}
