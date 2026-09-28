<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class Silence extends AbstractSpell
{
	public function __construct(private readonly int $minutes, int $cost)
	{
		parent::__construct($cost);
	}

	public function isOffensive(): bool
	{
		return true;
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->battle_id || $target->battle_id) {
			throw new Exception('Нельзя наложить молчанку во время боя');
		}

		if ($caster->is($target)) {
			throw new Exception('Нельзя наложить молчанку на себя');
		}

		if ($target->silence?->isFuture()) {
			throw new Exception('На персонаже уже действует молчанка');
		}

		$target->silence = now()->addMinutes($this->minutes);

		return $caster->name . ' наложил на персонажа ' . $target->name . ' молчанку на ' . $this->minutes . ' минут.';
	}
}
