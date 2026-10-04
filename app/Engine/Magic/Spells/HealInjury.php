<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;

class HealInjury extends AbstractSpell
{
	public function __construct(private readonly ?int $maxSeverity, int $cost, private readonly ?int $maxLevel = null)
	{
		parent::__construct($cost);
	}

	public function ignoresMagicProtection(): bool
	{
		return true;
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->battle_id || $target->battle_id) {
			throw new Exception('В бою лечение травм невозможно');
		}

		if ($this->maxLevel !== null && $target->level > $this->maxLevel) {
			throw new Exception('Этот свиток лечит только персонажей до ' . $this->maxLevel . '-го уровня');
		}

		if (!$target->injury?->isFuture()) {
			throw new Exception('Персонаж не травмирован');
		}

		if ($this->maxSeverity !== null && (!$target->injury_type || $target->injury_type > $this->maxSeverity)) {
			throw new Exception('Этим свитком такую травму не вылечить');
		}

		if ($caster->location !== $target->location) {
			throw new Exception('Для лечения нужно находиться в одной комнате с персонажем');
		}

		$target->injury = null;
		$target->injury_type = null;
		$target->effects()->where('type', 3)->delete();
		$target->unsetRelation('effects');

		return $caster->name . ' использовал «' . $item->title . '» и исцелил персонажа ' . $target->name . ' от травм.';
	}
}
