<?php

namespace App\Engine\Battle\Services;

use App\Engine\Battle\Data\TurnData;
use App\Engine\Battle\Enums\BattleStatus;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\User;
use Carbon\CarbonImmutable;

class TurnService
{
	/** @return array{hits: int, blocks: int} */
	public function getLimits(User $user): array
	{
		$hits = 1;
		$blocks = 1;

		foreach ($user->getSlot()->getItems() as $item) {
			if ($item->onset == 5 && $item->type == 5) {
				$blocks++;
			} elseif ($item->onset == 5 && $item->type == 1) {
				$hits++;
			}
		}

		return [
			'hits' => $hits,
			'blocks' => $blocks,
		];
	}

	public function submit(
		Battle $battle,
		BattleMember $fighter,
		User $user,
		TurnData $turn,
		CarbonImmutable $time,
	): ?string {
		if (
			$battle->status !== BattleStatus::ACTIVE
			|| $battle->result !== null
			|| $fighter->battle_id !== $battle->id
			|| $fighter->user_id !== $user->id
			|| $user->battle_id !== $battle->id
			|| $fighter->died_at
			|| $user->hp_now <= 0
		) {
			return 'Сейчас вы не можете сделать ход';
		}

		if ($turn->round !== $battle->round) {
			return 'Раунд уже изменился. Выберите действие заново';
		}

		if ($battle->round_at->addSeconds($battle->timeout)->lessThanOrEqualTo($time)) {
			return 'Время хода истекло';
		}

		if ($fighter->finished_at) {
			return 'Вы уже завершили ход';
		}

		$limits = $this->getLimits($user);

		if (count($turn->hits) > $limits['hits']) {
			return 'Выбрано больше ударов, чем разрешено';
		}

		if (count($turn->blocks) > $limits['blocks']) {
			return 'Выбрано больше блоков, чем разрешено';
		}

		if (empty($turn->hits) || empty($turn->blocks) || $turn->opponentId <= 0) {
			return 'Выберите удары, блоки и противника';
		}

		$opponent = $battle->members
			->where('id', $turn->opponentId)
			->where('side', $fighter->side == 1 ? 0 : 1)
			->whereNull('died_at')
			->first();

		if (!$opponent || $opponent->user->hp_now <= 0) {
			return 'Противник не найден';
		}

		$fighter->finished_at = $time;
		$fighter->save();

		$log = $battle->logs()->make();
		$log->date = $time;
		$log->round = $turn->round;
		$log->hit = $turn->hits;
		$log->block = $turn->blocks;
		$log->member()->associate($fighter);
		$log->enemy()->associate($opponent);
		$log->save();

		return null;
	}
}
