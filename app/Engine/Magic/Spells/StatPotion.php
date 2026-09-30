<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\Effect;
use App\Models\User;
use App\Models\UserItem;

class StatPotion extends Potion
{
	/** @param array{strength?: int, dexterity?: int, agility?: int, vitality?: int, magic?: int, intelligence?: int} $stats */
	public function __construct(
		private readonly array $stats,
		private readonly int $hours = 4,
	)
	{
		parent::__construct();
	}

	protected function applyPotion(User $caster, User $target, UserItem $item): string
	{
		if ($target->effects()->where('type', Effect::POTION)->whereFuture('date')->exists()) {
			throw new Exception('На персонаже может действовать только одно зелье');
		}

		$target->effects()->create([
			...$this->stats,
			'type' => Effect::POTION,
			'date' => now()->addHours($this->hours),
		]);

		return $target->name . ' выпил «' . $item->title . '». Характеристики изменены на ' . $this->hours . ' часа.';
	}
}
