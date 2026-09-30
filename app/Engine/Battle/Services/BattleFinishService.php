<?php

namespace App\Engine\Battle\Services;

use App\Engine\Battle\Enums\BattleResult;
use App\Engine\Battle\Enums\BattleStatus;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class BattleFinishService
{
	public function __construct(private readonly RewardService $rewardService)
	{
	}

	/** Работает с участниками, загруженными и заблокированными BattleEngine. */
	public function finishIfReady(
		Battle $battle,
		BattleMember $fighter,
		User $user,
		CarbonImmutable $time,
	): void {
		if (DB::transactionLevel() === 0) {
			throw new LogicException('Завершение боя должно выполняться в транзакции BattleEngine');
		}

		if (!in_array($battle->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)) {
			return;
		}

		if ($fighter->battle_id !== $battle->id || $fighter->user_id !== $user->id) {
			throw new InvalidArgumentException('Участник не принадлежит этому игроку и бою');
		}

		if ($user->hp_now <= 0 && !$fighter->died_at) {
			$fighter->died_at = $time;
			$fighter->save();
		}

		$result = $battle->result ?? $this->determineResult($battle);

		if ($result === null) {
			return;
		}

		$battle->result = $result;
		$battle->status = BattleStatus::FINISHED;
		$battle->save();

		User::query()
			->where('rank', 60)
			->whereBelongsTo($battle)
			->update([
				'online' => $time,
				'battle_id' => null,
			]);

		// После расчёта battle_id сбрасывается, поэтому повторный вызов не выдаёт награду.
		if ($user->battle_id !== $battle->id || $user->isBot()) {
			return;
		}

		$personalResult = $result->forSide($fighter->side);

		$this->rewardService->award($battle, $fighter, $user, $personalResult, $time);

		$user->battle()->associate(null);
		$user->save();
	}

	private function determineResult(Battle $battle): ?BattleResult
	{
		$aliveMembers = $battle->members
			->whereNull('died_at')
			->filter(fn(BattleMember $member) => $member->user->hp_now > 0);

		$firstTeamAlive = $aliveMembers->contains(fn(BattleMember $member) => $member->side == 0);
		$secondTeamAlive = $aliveMembers->contains(fn(BattleMember $member) => $member->side == 1);

		if ($firstTeamAlive && $secondTeamAlive) {
			return null;
		}

		if (!$firstTeamAlive && !$secondTeamAlive) {
			return BattleResult::DRAW;
		}

		return $firstTeamAlive ? BattleResult::FIRST_TEAM_WIN : BattleResult::SECOND_TEAM_WIN;
	}
}
