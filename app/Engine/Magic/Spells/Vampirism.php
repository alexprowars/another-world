<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class Vampirism extends DamageSpell
{
	public function __construct()
	{
		parent::__construct(0, 15);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->is($target)) {
			throw new Exception('Нельзя высасывать жизненную энергию у себя');
		}

		if ($caster->battle_id !== $target->battle_id) {
			throw new Exception('Персонажи находятся в разных боях');
		}

		if ($target->hp_now <= 5) {
			throw new Exception('Персонаж слишком слаб для высасывания жизненной энергии');
		}

		if ($target->vampire_protection?->isFuture()) {
			throw new Exception('Персонаж защищён от вампиров');
		}

		$amount = $this->applyResistance($target, floor($target->hp_now / 2));
		$damage = $this->applyDamage($caster, $target, $amount);
		$healed = min($damage, max(0, $caster->hp_max - $caster->hp_now));
		$caster->hp_now += $healed;

		return $caster->name . ' высосал у персонажа ' . $target->name . ' ' . $damage . ' HP и восстановил себе ' . $healed . ' HP.';
	}
}
