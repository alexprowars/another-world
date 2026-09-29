<?php

namespace App\Http\Resources;

use App\Models\BattleLog;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property BattleLog $resource
 */
class BattleLogResource extends JsonResource
{
	public function toArray($request): array
	{
		$log = $this->resource;
		$viewer = $request->user();

		return [
			'id' => $log->id,
			'date' => $log->date->toAtomString(),
			'round' => $log->round,
			'user' => $log->member->user->name,
			'side' => $log->member->side,
			'hits' => $log->hit,
			'damage' => $log->damage,
			'blocks' => $log->block,
			'enemy' => $log->enemy?->user->name,
			'enemy_blocks' => $log->enemy_block,
			'comment' => $log->comment_id,
			'my' => $viewer !== null && (
				$viewer->id === $log->member->user_id
				|| $viewer->id === $log->enemy?->user_id
			),
		];
	}
}
