<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\Effect;
use App\Models\User;
use App\Models\UserItem;

abstract class Potion extends AbstractSpell
{
	public function cast(User $caster, User $target, UserItem $item): string
	{
		$this->validateTarget($caster, $target);

		if ($target->poison >= 100) {
			throw new Exception('Организм слишком отравлен для употребления зелий');
		}

		$message = $this->applyPotion($caster, $target, $item);
		$message .= $this->applyPoison($target, $item);

		return $message;
	}

	protected function validateTarget(User $caster, User $target): void
	{
		$this->requireSelf($caster, $target);
	}

	abstract protected function applyPotion(User $caster, User $target, UserItem $item): string;

	private function applyPoison(User $target, UserItem $item): string
	{
		$poison = max(0, $target->poison ?? 0);
		$message = '';

		if (random_int(0, 100) < $poison) {
			$stats = [
				'strength' => 'Сила',
				'dexterity' => 'Удача',
				'agility' => 'Ловкость',
				'vitality' => 'Выносливость',
				'intelligence' => 'Разум',
				'battery' => 'Активность',
			];
			$stat = array_keys($stats)[random_int(0, count($stats) - 1)];
			$penalty = $poison < 25 ? 1 : ($poison < 50 ? 2 : 3);
			$minutes = $poison < 25 ? 30 : ($poison < 50 ? 60 : 120);

			$target->effects()->create([
				'type' => Effect::POISON,
				'date' => now()->addMinutes($minutes),
				$stat => -$penalty,
			]);
			$target->unsetRelation('effects');

			$message = ' Отравление уменьшило показатель «' . $stats[$stat] . '» на ' . $penalty . ' на ' . $minutes . ' мин.';
		}

		$target->poison = min(100, max(0, $poison + ($item->poison ?? 0)));

		return $message;
	}
}
