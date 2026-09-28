<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class CleansePoison extends AbstractSpell
{
	public function cast(User $caster, User $target, UserItem $item): string
	{
		$this->requireOutsideBattle($caster, $target);

		if ($caster->room !== $target->room) {
			throw new Exception('Для очищения нужно находиться в одной комнате с персонажем');
		}

		if ($target->poison <= 0) {
			throw new Exception('Персонаж не отравлен');
		}

		$target->poison = 0;

		return $caster->name . ' очистил организм персонажа ' . $target->name . ' от накопленного отравления.';
	}
}
