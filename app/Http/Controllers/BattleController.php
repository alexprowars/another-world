<?php

namespace App\Http\Controllers;

use App\Engine\Battle\Battle as BattleEngine;
use App\Engine\Battle\BattleStatus;
use App\Engine\Battle\BattleType;
use App\Engine\Map\Arena\Training;
use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Resources\BattleOfferResource;
use App\Models\Battle;
use App\Models\User;
use App\Services\BattleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BattleController extends Controller
{
	public function index(Request $request): Response|JsonResponse|RedirectResponse
	{
		$user = $request->user();

		if ($user->battle && in_array($user->battle->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)) {
			if ($request->isMethod('post')) {
				return to_route('battle');
			}

			if ($request->expectsJson() && !$request->header('X-Inertia')) {
				$result = DB::transaction(function () use ($user) {
					$battleModel = Battle::query()
						->lockForUpdate()
						->findOrFail($user->battle_id);

					$user = User::query()
						->lockForUpdate()
						->findOrFail($user->id);

					if ($user->battle_id !== $battleModel->id) {
						return ['action' => 'reload'];
					}

					$battle = new BattleEngine($battleModel, $user);
					$battle->init();

					return $battle->show();
				});

				return response()->json($result);
			}

			return Inertia::render('Battle', ['id' => $user->battle->id]);
		}

		if ($user->room == 2 && !$request->has('action')) {
			return new Training()();
		}

		if ($request->isMethod('post')) {
			return $this->updateOffer($request);
		}

		$userOffer = BattleService::getCurrentUserRequest($user);

		$battleType = $userOffer?->battle->type
			?? BattleType::from(min(3, max(1, $request->integer('battle_type', 1))));

		if ($userOffer && $battleType !== BattleType::DUEL) {
			if (BattleService::startOffer($user, $battleType)) {
				return to_route('battle');
			}

			$userOffer = $userOffer->fresh(['battle']);
		}

		$userOffer?->battle->load('members.user.tribe');

		$offers = Battle::query()
			->with('members.user.tribe')
			->where('status', BattleStatus::WAITING)
			->where('type', $battleType)
			->where('started_at', '>', now())
			->whereHas('members')
			->when($userOffer, fn(Builder $query) => $query->whereNot('id', $userOffer->battle_id))
			->when($battleType === BattleType::DUEL, fn(Builder $query) => $query->has('members', '=', 1))
			->orderByDesc('started_at')
			->get();

		$offerError = null;

		try {
			BattleService::offerValidation($user, $battleType);
		} catch (Exception $e) {
			$offerError = $e->getMessage();
		}

		return Inertia::render('Battle/Offers', [
			'battleType' => $battleType->value,
			'offers' => BattleOfferResource::collection($offers),
			'currentOffer' => $userOffer ? BattleOfferResource::make($userOffer->battle) : null,
			'currentSide' => $userOffer?->side,
			'offerError' => $offerError,
			'canTeleport' => !in_array($user->room, [1, 2, 3, 4], true) && !$user->r_type && !$user->prison_until?->isFuture(),
		]);
	}

	private function updateOffer(Request $request): RedirectResponse
	{
		$request->validate([
			'action' => ['required', 'in:create,take,withdraw,dismiss,start,teleport'],
			'battle_type' => ['sometimes', 'integer', 'in:1,2,3'],
			'offer' => ['required_if:action,take', 'integer', 'min:1'],
			'battle_side' => ['sometimes', 'integer', 'in:0,1'],
			'timeout' => ['sometimes', 'integer', 'in:1,3,5,10'],
			'comment' => ['nullable', 'string', 'max:255'],
			'offer_level' => ['sometimes', 'integer', 'in:1,2,3,4'],
			'time_battle_start' => ['sometimes', 'integer', 'in:180,300,600,900'],
			'capacity' => ['sometimes', 'integer', 'between:2,25'],
			'blood' => ['sometimes', 'boolean'],
			'unarmed' => ['sometimes', 'boolean'],
		]);

		$user = $request->user();

		$battleType = BattleType::from($request->integer('battle_type', 1));

		try {
			switch ($request->input('action')) {
				case 'create':
					BattleService::createOffer($user, $battleType, [
						'timeout' => $request->integer('timeout', 3),
						'comment' => $request->input('comment', ''),
						'offer_level' => $request->integer('offer_level', 1),
						'time_battle_start' => $request->integer('time_battle_start', 180),
						'capacity' => $request->integer('capacity', 2),
						'blood' => $request->boolean('blood'),
						'unarmed' => $request->boolean('unarmed'),
					]);

					break;
				case 'take':
					$battle = Battle::query()->find($request->integer('offer'));

					if (!$battle) {
						throw new Exception('Заявки не существует или истёк срок её размещения');
					}

					$battleType = $battle->type;

					BattleService::takeOffer($battle, $user, $request->integer('battle_side'));

					break;
				case 'withdraw':
					BattleService::withdrawOffer($user);

					break;
				case 'dismiss':
					BattleService::withdrawOffer($user, true);

					break;
				case 'start':
					$offer = BattleService::getCurrentUserRequest($user);

					if (!$offer || !BattleService::startOffer($user, $offer->battle->type)) {
						flash('Бой пока не может начаться. Обновите список заявок.');
					}

					break;
				case 'teleport':
					if ($user->r_type || $user->prison_until?->isFuture()) {
						throw new Exception('Сейчас вы не можете переместиться на арену');
					}

					$user->room = 1;
					$user->save();

					break;
			}
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('battle', ['battle_type' => $battleType->value]);
	}
}
