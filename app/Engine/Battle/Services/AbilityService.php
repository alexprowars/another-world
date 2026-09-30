<?php

namespace App\Engine\Battle\Services;

use App\Engine\Battle\Abilities\Ability;
use App\Engine\Battle\Abilities\AbilityEffect;
use App\Engine\Battle\Abilities\AbilityRegistry;
use App\Engine\Battle\Enums\BattleStatus;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\User;
use Carbon\CarbonImmutable;

class AbilityService
{
	public function activate(
		Battle $battle,
		BattleMember $fighter,
		User $user,
		int $abilityId,
		CarbonImmutable $time,
	): ?string {
		$ability = AbilityRegistry::find($abilityId);

		if ($ability === null) {
			return 'Такого приёма не существует';
		}

		if (!$user->abilities()->where('ability', $abilityId)->exists()) {
			return 'Этот приём не выбран у персонажа';
		}

		$error = $this->getError($battle, $fighter, $user, $ability, $time);

		if ($error !== null) {
			return $error;
		}

		$fighter->ability = $ability->id;
		$fighter->wait = $ability->wait;
		$fighter->time = $ability->duration;
		$fighter->hits -= $ability->hitCost;
		$fighter->blocks -= $ability->blockCost;
		$fighter->crits -= $ability->critCost;
		$fighter->spirit -= $ability->spiritCost;
		$fighter->parry -= $ability->parryCost;
		$fighter->hp -= $ability->hpCost;
		$fighter->save();

		return null;
	}

	public function getError(
		Battle $battle,
		BattleMember $fighter,
		User $user,
		Ability $ability,
		CarbonImmutable $time,
	): ?string {
		if (
			$battle->status !== BattleStatus::ACTIVE
			|| $battle->result !== null
			|| $fighter->died_at
			|| $user->hp_now <= 0
		) {
			return 'Сейчас вы не можете использовать приёмы';
		}

		if ($fighter->finished_at) {
			return 'Вы уже завершили ход';
		}

		if ($battle->round_at->addSeconds($battle->timeout)->lessThanOrEqualTo($time)) {
			return 'Время хода истекло';
		}

		if ($user->level < $ability->level) {
			return 'Ваш уровень слишком мал для этого приёма';
		}

		if ($fighter->wait > 0) {
			return 'Дождитесь окончания текущего приёма';
		}

		if (
			$fighter->hits < $ability->hitCost
			|| $fighter->blocks < $ability->blockCost
			|| $fighter->crits < $ability->critCost
			|| $fighter->spirit < $ability->spiritCost
			|| $fighter->parry < $ability->parryCost
			|| $fighter->hp < $ability->hpCost
		) {
			return 'Недостаточно боевых очков для этого приёма';
		}

		return null;
	}

	public function getEffect(BattleMember $fighter): AbilityEffect
	{
		if ($fighter->wait != 1) {
			return new AbilityEffect();
		}

		$ability = AbilityRegistry::find($fighter->ability);

		return $ability === null ? new AbilityEffect() : $ability->effect;
	}

	public function applyAttackEffect(BattleMember $fighter): void
	{
		$effect = $this->getEffect($fighter);

		$fighter->user->min += $effect->damageBonus;
		$fighter->user->max += $effect->damageBonus;
		$fighter->user->krit += $effect->critBonus;
		$fighter->user->uv += $effect->dodgeBonus;
	}

	public function finishAttack(BattleMember $fighter): void
	{
		$effect = $this->getEffect($fighter);

		if ($effect->healing > 0) {
			$fighter->user->hp_now = min(
				$fighter->user->hp_now + $effect->healing,
				$fighter->user->hp_max,
			);
		}

		if ($fighter->wait == 1) {
			$fighter->time = max($fighter->time - 1, 0);

			if ($fighter->time == 0) {
				$fighter->wait = 0;
			}
		}

		if ($fighter->wait > 1) {
			$fighter->wait -= 1;
		}
	}
}
