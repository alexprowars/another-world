<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class MageArmor extends AbstractSpell
{
	public function __construct()
	{
		parent::__construct(10);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($target->magic_protection?->isFuture()) {
			throw new Exception('На персонаже уже действует защита от магии');
		}

		$target->magic_protection = now()->addHour();

		return $caster->name . ' наложил на персонажа ' . $target->name . ' защиту от магии на один час.';
	}
}
