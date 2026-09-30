<?php

namespace App\Http\Controllers;

use App\Engine\Battle\BattleEngine;
use App\Engine\Battle\Enums\BattleStatus;
use App\Http\Controller;
use App\Http\Requests\BattleActionRequest;
use App\Http\Resources\BattleStateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BattleController extends Controller
{
	public function index(Request $request): Response|RedirectResponse
	{
		$battle = $request->user()->battle;

		if (!$battle || !in_array($battle->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)) {
			return to_route('arena');
		}

		return Inertia::render('Battle', ['id' => $battle->id]);
	}

	public function store(BattleActionRequest $request): JsonResponse
	{
		$user = $request->user();

		$battle = $user->battle;

		if (!$battle || !in_array($battle->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)) {
			return response()->json(['action' => 'reload']);
		}

		$turn = $request->turnData();

		$lastLogId = $request->integer('lastLogId');
		$abilityId = $request->has('ability') ? $request->integer('ability') : null;

		$state = new BattleEngine($battle, $user)
			->process($turn, $lastLogId, $abilityId);

		$result = $state === null
			? ['action' => 'reload']
			: BattleStateResource::make($state)->resolve($request);

		return response()->json($result);
	}
}
