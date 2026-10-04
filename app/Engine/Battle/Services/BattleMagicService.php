<?php

namespace App\Engine\Battle\Services;

use App\Events\BattleUpdated;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\User;
use Carbon\CarbonImmutable;

class BattleMagicService
{
	public function __construct(private readonly BattleFinishService $finishService)
	{
	}

	public function process(Battle $battle, User $caster, User $target, ?string $message): void
	{
		$time = CarbonImmutable::now();

		$members = $battle->members()
			->with('user')
			->orderBy('id')
			->lockForUpdate()
			->get();

		$battle->setRelation('members', $members);

		if ($message !== null) {
			$fighter = $members->where('user_id', $caster->id)
				->firstOrFail();

			$enemy = $members->where('user_id', $target->id)
				->firstOrFail();

			$battle->logs()
				->make([
					'date' => $time,
					'round' => $battle->round,
					'message' => $message,
				])
				->member()->associate($fighter)
				->enemy()->associate($enemy)
				->save();
		}

		$this->finishService->resolveResult($battle, $time);

		$userIds = $members
			->filter(fn(BattleMember $member) => $member->user_id !== $caster->id && !$member->user->isBot())
			->pluck('user_id')
			->values()
			->all();

		if (!empty($userIds)) {
			BattleUpdated::dispatch($battle->id, $userIds);
		}
	}
}
