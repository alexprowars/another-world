<?php

namespace App\Http\Resources;

use App\Engine\Battle\Enums\BattleType;
use App\Models\Battle;
use App\Models\BattleMember;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Battle $resource
 */
class BattleOfferResource extends JsonResource
{
	public function toArray($request): array
	{
		$battle = $this->resource;

		return [
			...$battle->only(['id', 'type', 'timeout', 'comment', 'capacity', 'min_level', 'max_level', 'is_blood', 'use_weapons']),
			'startedAt' => $battle->started_at?->toAtomString(),
			'readyAt' => $battle->started_at?->addSeconds($battle->type === BattleType::DUEL ? 0 : 10)->toAtomString(),
			'members' => $battle->members->map(fn(BattleMember $member) => [
				'id' => $member->id,
				'side' => $member->side,
				'user' => [
					...$member->user->only(['id', 'name', 'level', 'rank']),
					'tribe' => $member->user->tribe?->only(['id', 'name']),
				],
			]),
		];
	}
}
