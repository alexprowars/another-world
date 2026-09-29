<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Http\Resources\BattleLogResource;
use App\Models\Battle;
use App\Models\BattleMember;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class BattleLogController extends Controller
{
	public function index(int $id): Response
	{
		$battle = Battle::query()
			->with('members.user')
			->findOrFail($id);

		$logs = $battle->logs()
			->with(['member.user', 'enemy.user'])
			->where('comment_id', '>', 0)
			->when(!$battle->result, fn(Builder $query) => $query->where('round', '<', $battle->round))
			->orderByDesc('round')
			->orderByDesc('id')
			->get();

		return Inertia::render('Battle/Log', [
			'battle' => [
				...$battle->only(['id', 'type', 'status', 'result']),
				'startedAt' => $battle->started_at?->toAtomString(),
			],
			'members' => $battle->members->map(fn(BattleMember $member) => [
				...$member->user->only(['id', 'name', 'level']),
				'side' => $member->side,
			]),
			'logs' => BattleLogResource::collection($logs),
		]);
	}
}
