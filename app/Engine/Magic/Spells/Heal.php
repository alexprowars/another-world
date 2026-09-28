<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class Heal extends AbstractSpell
{
	public function __construct(private readonly int $amount, int $cost)
	{
		parent::__construct($cost);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		$healing = min($this->amount, $target->hp_max - $target->hp_now);

		if ($healing <= 0) {
			throw new Exception('Персонаж не нуждается в восстановлении здоровья');
		}

		$target->hp_now += $healing;

		return $caster->name . ' использовал «' . $item->title . '». Здоровье персонажа ' . $target->name . ' восстановлено на ' . $healing . ' HP.';
	}
}
