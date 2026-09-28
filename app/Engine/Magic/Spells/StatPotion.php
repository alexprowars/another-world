<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\Effect;
use App\Models\User;
use App\Models\UserItem;

class StatPotion extends Potion
{
	/** @param array{strength?: int, dexterity?: int, agility?: int, vitality?: int, magic?: int, intelligence?: int} $stats */
	public function __construct(private readonly array $stats)
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
			'date' => now()->addHours(4),
		]);

		return $target->name . ' выпил «' . $item->title . '». Характеристики изменены на 4 часа.';
	}
}
