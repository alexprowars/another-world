<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\Level;
use App\Models\User;
use App\Models\UserItem;
use App\Services\InventoryService;

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
			's_strength' => 3,
			's_dexterity' => 3,
			's_agility' => 3,
			's_vitality' => 3,
			's_magic' => 1,
			's_intelligence' => 0,
			'updates' => $updates,
			'hp_now' => 15,
			'energy_now' => 5,
		]);

		return 'Вы использовали «' . $item->title . '». Характеристики сброшены. Очков для распределения: ' . $updates . '.';
	}
}
