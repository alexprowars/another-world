<?php

namespace App\Http\Resources;

use App\Engine\Battle\Data\BattleState;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property BattleState $resource
 */
class BattleStateResource extends JsonResource
{
	public function toArray($request): array
	{
		$state = $this->resource;

		$result = [
			'time' => $state->time->toAtomString(),
			'action' => $this->action(),
			'result' => $this->battleResult(),
			'opponents' => array_map(fn($opponent) => [
				'id' => $opponent->id,
				'name' => $opponent->user->name,
				'level' => $opponent->user->level,
			], $state->opponents),
			'kicks' => $state->limits['hits'],
			'blocks' => $state->limits['blocks'],
			'abilities' => $this->abilities(),
			'user' => $this->participant($state->user, $state->userItems, $state->user->tribe),
			'teams' => $this->teams(),
			'opponent_id' => $state->opponent?->id,
			'opponent' => $state->opponent === null ? null : $this->participant(
				$state->opponent->user,
				$state->opponentItems,
				$state->opponent->user->tribe_id,
			),
			'damage' => $state->fighter->damage,
			'id' => $state->user->battle_id,
			'round' => $state->battle->round,
			'timeout_left' => $state->timeoutLeft,
			'timeout' => $state->battle->timeout,
			'logs' => BattleLogResource::collection($state->logs)->resolve($request),
		];

		if ($state->message !== null) {
			$result['m'] = $state->message;
		}

		return $result;
	}

	private function action(): string
	{
		$state = $this->resource;

		if ($state->battle->result !== null) {
			return 'finishBattle';
		}

		if ($state->user->hp_now <= 0 || $state->fighter->died_at) {
			return 'userDead';
		}

		if ($state->fighter->finished_at) {
			return 'waitImpact';
		}

		return $state->roundExpired ? 'refresh' : 'impactForm';
	}

	private function battleResult(): ?string
	{
		$state = $this->resource;

		return $state->battle->result?->forSide($state->fighter->side)->value;
	}

	private function participant(User $user, array $items, mixed $tribe): array
	{
		return [
			'id' => $user->id,
			'rank' => $user->rank,
			'hp' => (int) floor($user->hp_now),
			'hp_max' => $user->hp_max,
			'energy' => (int) floor($user->energy_now),
			'energy_max' => $user->energy_max,
			'level' => $user->level,
			'tribe' => $tribe,
			'name' => $user->name,
			'avatar' => $user->getAvatar(),
			'items' => $items,
		];
	}

	private function teams(): array
	{
		$teams = ['left' => [], 'right' => []];

		if ($this->resource->battle->result !== null) {
			return $teams;
		}

		$members = collect($this->resource->members)->sortBy([
			['user.rank', 'asc'],
			['user.level', 'asc'],
		]);

		foreach ($members as $member) {
			$teams[$member->side == 0 ? 'left' : 'right'][] = [
				'id' => $member->user->id,
				'name' => $member->user->name,
				'hp' => (int) floor($member->user->hp_now),
				'level' => $member->user->level,
				'side' => $member->side,
				'finished' => $member->finished_at !== null,
			];
		}

		return $teams;
	}

	private function abilities(): array
	{
		$state = $this->resource;
		$list = [];

		for ($slot = 1; $slot <= 10; $slot++) {
			$list['p_' . $slot] = null;
		}

		foreach ($state->abilities as $slot => $entry) {
			$ability = $entry['ability'];

			$list['p_' . $slot] = [
				'id' => $ability->id,
				'n' => $ability->name,
				'b' => $ability->blockCost,
				'h' => $ability->hitCost,
				'k' => $ability->critCost,
				'm' => $ability->spiritCost,
				'p' => $ability->parryCost,
				'd' => $ability->hpCost,
				'a' => $ability->description,
				'w' => $entry['available'] ? 0 : 1,
			];
		}

		return [
			'list' => $list,
			'wait' => $state->fighter->wait,
			'time' => $state->fighter->time,
			'ability' => $state->activeAbility?->name,
			'points' => [
				'blocks' => $state->fighter->blocks,
				'hits' => $state->fighter->hits,
				'crits' => $state->fighter->crits,
				'magic' => $state->fighter->spirit,
				'parry' => $state->fighter->parry,
				'hp' => $state->fighter->hp,
			],
		];
	}
}
