<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class Snowball extends DamageSpell
{
	public function __construct()
	{
		parent::__construct(10, 0);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->is($target)) {
			throw new Exception('Нельзя бросать снежок в себя');
		}

		if ($caster->battle_id !== $target->battle_id) {
			throw new Exception('Персонажи находятся в разных боях');
		}

		if ($target->hp_now < 50) {
			throw new Exception('Для попадания снежком у персонажа должно быть хотя бы 50 HP');
		}

		$damage = $this->applyDamage($caster, $target, 10);

		return $caster->name . ' попал снежком в персонажа ' . $target->name . ' и нанёс ' . $damage . ' HP урона.';
	}
}
