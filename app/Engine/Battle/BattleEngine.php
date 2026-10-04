<?php

namespace App\Engine\Battle;

use App\Engine\Battle\Abilities\AbilityRegistry;
use App\Engine\Battle\Data\BattleState;
use App\Engine\Battle\Data\TurnData;
use App\Engine\Battle\Enums\BattleStatus;
use App\Engine\Battle\Services\AbilityService;
use App\Engine\Battle\Services\AttackCalculator;
use App\Engine\Battle\Services\BattleFinishService;
use App\Engine\Battle\Services\RewardService;
use App\Engine\Battle\Services\RoundResolver;
use App\Engine\Battle\Services\TurnService;
use App\Events\BattleUpdated;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Random\Randomizer;

class BattleEngine
{
	private readonly RoundResolver $roundResolver;
	private readonly RewardService $rewardService;
	private readonly AbilityService $abilityService;
	private readonly TurnService $turnService;
	private readonly BattleFinishService $finishService;

	protected BattleMember $fighter;

	public function __construct(
		protected Battle $battle,
		protected User $user,
	) {
		$randomizer = new Randomizer();

		$this->rewardService = new RewardService($randomizer);
		$this->abilityService = new AbilityService();
		$this->turnService = new TurnService();
		$this->finishService = new BattleFinishService($this->rewardService);
		$this->roundResolver = new RoundResolver(
			new AttackCalculator($randomizer),
			$this->rewardService,
			$this->abilityService,
			$randomizer,
		);
	}

	public function process(TurnData $turn, int $lastLogId, ?int $abilityId): ?BattleState
	{
		return DB::transaction(function () use ($turn, $lastLogId, $abilityId) {
			$this->battle = Battle::query()
				->lockForUpdate()
				->findOrFail($this->battle->id);

			$this->user = User::query()
				->lockForUpdate()
				->findOrFail($this->user->id);

			if ($this->user->battle_id !== $this->battle->id) {
				return null;
			}

			$time = CarbonImmutable::now();

			$this->loadParticipants();

			return $this->processState($turn, $lastLogId, $abilityId, $time);
		});
	}

	private function loadParticipants(): void
	{
		$members = $this->battle->members()
			->orderBy('id')
			->lockForUpdate()
			->get();

		$users = User::query()
			->whereIn('id', $members->pluck('user_id'))
			->whereKeyNot($this->user->id)
			->get()
			->keyBy('id');

		$users->put($this->user->id, $this->user);

		foreach ($members as $member) {
			$member->setRelation('battle', $this->battle);
			$member->setRelation('user', $users->get($member->user_id));
		}

		$this->battle->setRelation('members', $members);
		$this->fighter = $members->where('user_id', $this->user->id)->firstOrFail();
	}

	private function processState(TurnData $turn, int $lastLogId, ?int $abilityId, CarbonImmutable $time): BattleState
	{
		$this->user->calculate(time: $time);

		$roundExpired = $this->roundResolver->isExpired($this->battle, $time);

		$message = $this->processActions($turn, $abilityId, $roundExpired, $time);

		$this->finishService->finishIfReady($this->battle, $this->fighter, $this->user, $time);

		return $this->prepareState($turn, $lastLogId, $roundExpired, $message, $time);
	}

	private function processActions(TurnData $turn, ?int $abilityId, bool $roundExpired, CarbonImmutable $time): ?string
	{
		$message = null;
		$battleUpdated = false;

		if ($roundExpired && $this->battle->status === BattleStatus::ACTIVE && $this->battle->result === null) {
			$battleUpdated = $this->roundResolver->resolve($this->battle, $time);
		}

		$isCurrentRound = $turn->round === $this->battle->round;

		if (!$isCurrentRound && ($abilityId !== null || $turn->hasActions())) {
			$message = 'Раунд уже изменился. Выберите действие заново';
		}

		if (!$roundExpired && $isCurrentRound && $abilityId !== null) {
			$message = $this->abilityService->activate(
				$this->battle,
				$this->fighter,
				$this->user,
				$abilityId,
				$time,
			);

			if ($message === null) {
				$battleUpdated = true;
			}
		}

		if (!$roundExpired && $this->battle->status === BattleStatus::ACTIVE && $this->battle->result === null) {
			if ($turn->hasActions()) {
				$turnError = $this->turnService->submit(
					$this->battle,
					$this->fighter,
					$this->user,
					$turn,
					$time,
				);

				if ($turnError !== null) {
					$message = $turnError;
				} else {
					$battleUpdated = true;
				}
			}

			if ($this->roundResolver->resolve($this->battle, $time)) {
				$battleUpdated = true;
			}
		}

		if ($battleUpdated) {
			$userIds = $this->battle->members
				->filter(fn (BattleMember $member) => $member->user_id !== $this->user->id && !$member->user->isBot())
				->pluck('user_id')
				->values()
				->all();

			if (!empty($userIds)) {
				BattleUpdated::dispatch($this->battle->id, $userIds);
			}
		}

		return $message;
	}

	private function prepareState(TurnData $turn, int $lastLogId, bool $roundExpired, ?string $message, CarbonImmutable $time): BattleState
	{
		$timeout = max(0, (int) ceil(
			$this->battle->timeout - $this->battle->round_at->diffInSeconds($time)
		));

		$limits = $this->turnService->getLimits($this->user);

		$this->user->loadMissing('tribe');

		$userItems = $this->user->getSlotsInfo();

		$members = $this->battle->members
			->whereNull('died_at')
			->filter(fn(BattleMember $member) => $member->user->hp_now > 0);

		$opponents = $members
			->where('side', $this->fighter->side == 1 ? 0 : 1);

		$opponent = null;

		$opponentItems = [];

		if ($this->battle->result === null && !$this->fighter->died_at && $this->user->hp_now > 0) {
			$opponent = $opponents->first();

			if (!$this->fighter->finished_at) {
				$opponent = $opponents->firstWhere('id', $turn->opponentId) ?? $opponent;
			}

			if ($opponent !== null && $timeout) {
				$opponent->user->loadMissing('tribe');
				$opponent->user->calculate(time: $time);

				$opponentItems = $opponent->user->getSlotsInfo();
			} else {
				$opponent = null;
			}
		} else {
			$opponents = $opponents->take(0);

			$limits = ['hits' => 0, 'blocks' => 0];
		}

		$abilities = [];

		$selectedAbilities = $this->user
			->abilities()
			->pluck('ability', 'slot');

		foreach ($selectedAbilities as $slot => $abilityId) {
			$ability = AbilityRegistry::find($abilityId);

			if ($ability === null) {
				continue;
			}

			$error = $this->abilityService->getError($this->battle, $this->fighter, $this->user, $ability, $time);

			$abilities[$slot] = [
				'ability' => $ability,
				'available' => $error === null,
			];
		}

		// Курсор не проходит скрытые ходы, даже если события магии уже показаны.
		$pendingLogId = $this->battle->result !== null ? null : $this->battle->logs()
			->where('round', '>=', $this->battle->round)
			->whereNotNull('hit')
			->whereNull('comment_id')
			->min('id');

		$logs = $this->battle->logs()
			->visible()
			->orderByDesc('round')
			->orderByDesc('id')
			->where('id', '>', $lastLogId)
			->get();

		$nextLogId = $logs->max('id') ?? $lastLogId;

		if ($pendingLogId !== null) {
			$nextLogId = min($nextLogId, $pendingLogId - 1);
		}

		$membersById = $this->battle->members->keyBy('id');

		foreach ($logs as $log) {
			$log->setRelation('member', $membersById->get($log->member_id));
			$log->setRelation('enemy', $membersById->get($log->enemy_id));
		}

		return new BattleState(
			battle: $this->battle,
			fighter: $this->fighter,
			user: $this->user,
			opponent: $opponent,
			members: $members->values()->all(),
			opponents: $opponents->values()->all(),
			abilities: $abilities,
			activeAbility: AbilityRegistry::find($this->fighter->ability),
			userItems: $userItems,
			opponentItems: $opponentItems,
			logs: $logs,
			lastLogId: max($lastLogId, $nextLogId),
			limits: $limits,
			time: $time,
			timeoutLeft: $timeout,
			roundExpired: $roundExpired,
			message: $message,
		);
	}
}
