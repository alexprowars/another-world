<?php

namespace App\Engine\Magic\Spells;

use App\Engine\Magic\Spell;
use App\Exceptions\Exception;
use App\Models\User;

abstract class AbstractSpell implements Spell
{
	public function __construct(private readonly int $cost = 0)
	{
	}

	public function manaCost(): int
	{
		return $this->cost;
	}

	public function isOffensive(): bool
	{
		return false;
	}

	public function canJoinBattle(): bool
	{
		return false;
	}

	public function ignoresMagicProtection(): bool
	{
		return false;
	}

	protected function requireOutsideBattle(User $caster, User $target): void
	{
		if ($caster->battle_id || $target->battle_id) {
			throw new Exception('Это заклинание нельзя использовать во время боя');
		}
	}

	protected function requireSelf(User $caster, User $target): void
	{
		if (!$caster->is($target)) {
			throw new Exception('Этот предмет можно использовать только на себя');
		}
	}
}
