<?php

namespace App\Http\Controllers;

use App\Engine\Battle\Enums\BattleStatus;
use App\Engine\Battle\Enums\BattleType;
use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Requests\ArenaActionRequest;
use App\Http\Resources\BattleOfferResource;
use App\Models\Battle;
use App\Services\BattleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArenaController extends Controller
{
	public function index(Request $request): Response|RedirectResponse
	{
		$user = $request->user();

		if ($user->battle && in_array($user->battle->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)) {
			return to_route('battle');
		}

		if ($user->room == 2) {
			return to_route('map');
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
			'canTeleport' => !in_array($user->room, [1, 2, 3, 4], true) && !$user->r_type && !$user->prison?->isFuture(),
		]);
	}

	public function store(ArenaActionRequest $request): RedirectResponse
	{
		$user = $request->user();

		if ($user->battle && in_array($user->battle->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)) {
			return to_route('battle');
		}

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

					if ($offer && BattleService::startOffer($user, $offer->battle->type)) {
						return to_route('battle');
					}

					flash('Бой пока не может начаться. Обновите список заявок.');

					break;
				case 'teleport':
					if ($user->r_type || $user->prison?->isFuture()) {
						throw new Exception('Сейчас вы не можете переместиться на арену');
					}

					$user->room = 1;
					$user->save();

					break;
			}
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('arena', ['battle_type' => $battleType->value]);
	}
}
