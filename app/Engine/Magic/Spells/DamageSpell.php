<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\BattleMember;
use App\Models\User;
use App\Models\UserItem;

abstract class DamageSpell extends AbstractSpell
{
	public function __construct(private readonly int $damage, int $cost)
	{
		parent::__construct($cost);
	}

	public function isOffensive(): bool
	{
		return true;
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if ($caster->is($target)) {
			throw new Exception('Вы не можете атаковать сами себя');
		}

		if (!$caster->battle_id || $caster->battle_id !== $target->battle_id) {
			throw new Exception('Для использования нужно находиться в одном бою с персонажем');
		}

		$damage = $this->applyDamage($caster, $target, $this->damageAmount($caster, $target));

		return $caster->name . ' использовал «' . $item->title . '» против ' . $target->name . ' и нанёс ' . $damage . ' HP урона.';
	}

	protected function applyDamage(User $caster, User $target, float $amount): float
	{
		$damage = min(max(0, $target->hp_now), max(0, $amount));

		$target->hp_now -= $damage;

		if (!$caster->battle_id) {
			return $damage;
		}

		BattleMember::query()
			->where('battle_id', $caster->battle_id)
			->whereBelongsTo($caster)
			->increment('damage', $damage);

		if ($target->hp_now <= 0) {
			BattleMember::query()
				->where('battle_id', $target->battle_id)
				->whereBelongsTo($target)
				->update(['died_at' => now()]);
		}

		return $damage;
	}

	protected function damageAmount(User $caster, User $target): float
	{
		$combatStats = $caster->getCombatStats();
		$minDamage = (int) round($combatStats->getMinMagicDamage());
		$maxDamage = (int) round($combatStats->getMaxMagicDamage());

		return $this->damage
			+ random_int($minDamage, $maxDamage)
			+ random_int(0, 5);
	}

	protected function applyResistance(User $target, float $damage): float
	{
		$resistance = min(100, max(0, $target->magic_resistance));

		return ceil($damage * (1 - $resistance / 100));
	}
}
