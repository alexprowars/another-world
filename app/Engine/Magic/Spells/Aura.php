<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\Effect;
use App\Models\User;
use App\Models\UserItem;

class Aura extends AbstractSpell
{
	/** @param array{armor1?: int, armor2?: int, armor3?: int, armor4?: int, armor5?: int, min?: int, max?: int} $stats */
	public function __construct(private readonly array $stats, private readonly int $hours)
	{
		parent::__construct(20);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($target->effects()->where('type', Effect::AURA)->exists()) {
			throw new Exception('На персонаже может действовать только одна аура');
		}

		$seconds = (int) ceil($this->hours * 3600 * (1 + $target->aura_duration_bonus / 100));

		$target->effects()->create([
			...$this->stats,
			'type' => Effect::AURA,
			'date' => now()->addSeconds($seconds),
		]);
		$target->unsetRelation('effects');

		return $caster->name . ' наложил на персонажа ' . $target->name . ' ауру «' . $item->title . '» на ' . ($seconds / 60) . ' мин.';
	}
}
