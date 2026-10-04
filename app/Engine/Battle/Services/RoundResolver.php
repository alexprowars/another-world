<?php

namespace App\Engine\Battle\Services;

use App\Engine\Battle\Data\AttackResult;
use App\Engine\Battle\Enums\BattleStatus;
use App\Engine\CombatStats;
use App\Engine\Services\BattleService;
use App\Models\Battle;
use App\Models\BattleLog;
use App\Models\BattleMember;
use App\Models\User;
use Carbon\CarbonImmutable;
use Random\Randomizer;

class RoundResolver
{
	public function __construct(
		private readonly AttackCalculator $attackCalculator,
		private readonly RewardService $rewardService,
		private readonly AbilityService $abilityService,
		private readonly Randomizer $randomizer,
	) {
	}

	public function resolve(Battle $battle, CarbonImmutable $time): bool
	{
		if ($battle->status !== BattleStatus::ACTIVE || $battle->result !== null) {
			return false;
		}

		if ($this->isExpired($battle, $time)) {
			$this->handleTimeout($battle, $time);
		}

		$pendingMembers = $battle->members
			->whereNull('finished_at')
			->whereNull('died_at')
			->filter(fn(BattleMember $member) => !$member->user->isBot() && $member->user->hp_now > 0);

		if ($pendingMembers->isNotEmpty()) {
			return false;
		}

		// Ходы ботов формируются автоматически, когда все живые игроки завершили ход.
		$this->generateBotTurns($battle, $time);
		$this->endRound($battle, $time);

		return true;
	}

	public function isExpired(Battle $battle, CarbonImmutable $time): bool
	{
		return $battle->round_at->addSeconds($battle->timeout)->lessThanOrEqualTo($time);
	}

	private function handleTimeout(Battle $battle, CarbonImmutable $time): void
	{
		$timedOutMembers = $battle->members
			->whereNull('finished_at')
			->whereNull('died_at')
			->filter(fn(BattleMember $member) => !$member->user->isBot() && $member->user->hp_now > 0);

		foreach ($timedOutMembers as $member) {
			if ($battle->round > 1) {
				$member->experience_base /= 2;
				$member->died_at = $time;
			}

			$member->finished_at = $time;
			$member->save();

			$battle->logs()
				->make([
					'date' => $time,
					'round' => $battle->round,
					'comment_id' => $battle->round > 1 ? 79 : 78,
				])
				->member()->associate($member)
				->save();
		}
	}

	private function generateBotTurns(Battle $battle, CarbonImmutable $time): void
	{
		$bots = $battle->members
			->whereNull('finished_at')
			->whereNull('died_at')
			->filter(fn(BattleMember $member) => $member->user->isBot() && $member->user->hp_now > 0);

		foreach ($bots as $bot) {
			$opponents = $battle->members
				->whereNull('died_at')
				->where('side', $bot->side == 0 ? 1 : 0)
				->filter(fn(BattleMember $member) => $member->user->hp_now > 0);

			if ($opponents->isEmpty()) {
				continue;
			}

			$opponent = $opponents->values()->get($this->randomizer->getInt(0, $opponents->count() - 1));

			$bot->user->calculate(time: $time);

			$hit = $this->randomizer->getInt(1, 5);
			$firstBlock = $this->randomizer->getInt(1, 5);
			$secondBlock = $this->randomizer->getInt(1, 5);

			while ($firstBlock == $secondBlock) {
				$secondBlock = $this->randomizer->getInt(1, 5);
			}

			$bot->finished_at = $time;
			$bot->save();

			$log = $battle->logs()->make();
			$log->date = $time;
			$log->round = $battle->round;
			$log->hit = [$hit];
			$log->block = [$firstBlock, $secondBlock];
			$log->member()->associate($bot);
			$log->enemy()->associate($opponent);
			$log->save();
		}
	}

	private function endRound(Battle $battle, CarbonImmutable $time): void
	{
		$logs = $battle->logs()
			->where('round', $battle->round)
			->whereNull('message')
			->orderBy('id')
			->get();

		$membersById = $battle->members->keyBy('id');

		foreach ($logs as $log) {
			$log->setRelation('member', $membersById->get($log->member_id));
		}

		$logsByMember = $logs->keyBy('member_id');

		// Погибшие в процессе расчёта сохраняют право выполнить уже выбранный удар.
		$aliveAtRoundStart = $logs
			->filter(fn(BattleLog $log) => !$log->member->died_at && $log->member->user->hp_now > 0)
			->pluck('member_id');

		// Бонусы приёмов фиксируются до расходования их длительности в атаках.
		$attackStatsByMember = [];

		foreach ($logs as $log) {
			$log->member->user->calculate(time: $time);

			$attackStatsByMember[$log->member_id] = $this->abilityService->getAttackStats($log->member);
		}

		foreach ($logs as $attackLog) {
			// Погибший от магии до расчёта раунда не выполняет ранее выбранный удар.
			if (!$aliveAtRoundStart->contains($attackLog->member_id)) {
				continue;
			}

			$defenceLog = $logsByMember->get($attackLog->enemy_id);

			if (!$defenceLog || $attackLog->member->side === $defenceLog->member->side) {
				continue;
			}

			$this->resolveAttack(
				$attackLog,
				$defenceLog,
				$attackStatsByMember[$attackLog->member_id],
				$attackStatsByMember[$defenceLog->member_id],
				$time,
			);
		}

		$battle->round++;
		$battle->round_at = $time;
		$battle->save();

		$battle->members->each(function (BattleMember $member) {
			$member->finished_at = null;
			$member->save();
		});
	}

	private function resolveAttack(
		BattleLog $attackLog,
		BattleLog $defenceLog,
		CombatStats $attackerStats,
		CombatStats $defenderStats,
		CarbonImmutable $time,
	): void {
		$attack = $this->attackCalculator->calculate(
			$attackerStats,
			$defenderStats,
			$attackLog->hit ?? [],
			$defenceLog->block ?? [],
		);

		// Гарантированные эффекты действуют только на первый соответствующий удар.
		$attackerStats->forceCrit = false;
		$defenderStats->forceDodge = false;

		$this->applyAttackResult($attackLog, $defenceLog, $attack, $time);
	}

	private function applyAttackResult(
		BattleLog $attackLog,
		BattleLog $defenceLog,
		AttackResult $attack,
		CarbonImmutable $time,
	): void {
		$attacker = $attackLog->member;
		$defender = $defenceLog->member;

		if (!$attacker->user->hp_max) {
			$attacker->user->hp_max = 1;
		}

		$this->rewardService->addAttackExperience($attacker, $defender, $attack);

		$attacker->crits += $attack->attackerCrits;
		$attacker->hits += $attack->attackerHits;
		$defender->parry += $attack->defenderParry;
		$defender->blocks += $attack->defenderBlocks;

		if ($attack->damage >= 10) {
			$attacker->hp += (int) round($attack->damage / 10);
		}

		$this->abilityService->finishAttack($attacker);

		if ($defender->user->hp_now - $attack->damage <= 0) {
			$this->applyInjury(
				$attacker->user,
				$defender->user,
				$attack->damage,
				$defender->user->hp_max,
				$time,
			);
		}

		$defender->save();

		$defender->user->hp_now = max(0, $defender->user->hp_now - $attack->damage);
		$defender->user->save();

		$attacker->damage += $attack->damage;
		$attacker->save();
		$attacker->user->save();

		$attackLog->damage = $attack->damage;
		$attackLog->enemy_block = $defenceLog->block ?? [];
		$attackLog->comment_id = $attack->commentId;
		$attackLog->save();
	}

	private function applyInjury(
		User $attacker,
		User $defender,
		int $damage,
		int|float $maxHealth,
		CarbonImmutable $time,
	): void {
		if ($damage >= $maxHealth * config('battle.injury_hard')) {
			BattleService::setInjury($attacker, $defender, 3, $time, $this->randomizer);
		} elseif ($damage >= $maxHealth * config('battle.injury_medium')) {
			BattleService::setInjury($attacker, $defender, 2, $time, $this->randomizer);
		} elseif ($damage >= $maxHealth * config('battle.injury_light')) {
			BattleService::setInjury($attacker, $defender, 1, $time, $this->randomizer);
		}
	}
}
