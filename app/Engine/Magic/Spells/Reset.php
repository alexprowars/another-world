<?php

namespace App\Engine\Magic\Spells;

use App\Engine\Services\InventoryService;
use App\Exceptions\Exception;
use App\Models\Level;
use App\Models\User;
use App\Models\UserItem;

class Reset extends AbstractSpell
{
	public function __construct()
	{
		parent::__construct(40);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->battle_id) {
			throw new Exception('Нельзя сбросить характеристики во время боя');
		}

		if (!$caster->is($target)) {
			throw new Exception('Это заклинание можно наложить только на себя');
		}

		$updates = Level::query()
			->where('exp', '<=', $target->exp)
			->sum('updates');

		InventoryService::unsetAllObject($target);

		$target->fill([
			'strength' => 3,
			'dexterity' => 3,
			'agility' => 3,
			'vitality' => 3,
			'magic' => 1,
			'intelligence' => 0,
			'updates' => $updates,
			'hp_now' => 15,
			'energy_now' => 5,
		]);

		return 'Вы использовали «' . $item->title . '». Характеристики сброшены. Очков для распределения: ' . $updates . '.';
	}
}
