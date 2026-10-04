<?php

namespace App\Engine\Locations;

use App\Engine\Services\GamblingHouseService;
use App\Exceptions\Exception;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class GamblingHouseController extends LocationController
{
	public function index(): Response|RedirectResponse
	{
		$request = request();

		$user = $request->user();
		$game = $request->query('game') === 'lottery' ? 'lottery' : 'dice';

		$lottery = null;

		if ($game === 'lottery') {
			$draw = GamblingHouseService::currentDraw();

			$tickets = $draw->tickets()
				->whereBelongsTo($user)
				->orderBy('number')
				->pluck('number');

			$history = LotteryDraw::query()
				->whereNotNull('drawn_at')
				->whereNotNull('winning_number')
				->orderByDesc('draws_at')
				->limit(10)
				->get()
				->map(fn (LotteryDraw $entry) => [
					'id' => $entry->id,
					'date' => $entry->draws_at->format('d.m.Y H:i'),
					'winner_id' => $entry->winner_id,
					'winner' => $entry->winner_name,
					'number' => $entry->winning_number,
					'prize' => $entry->prize,
				]);

			$pendingTickets = LotteryTicket::query()
				->whereBelongsTo($user)
				->whereHas('draw', function ($query) {
					$query->whereNull('drawn_at')->where('draws_at', '<=', now());
				})
				->count();

			$lottery = [
				'id' => $draw->id,
				'draws_at' => $draw->draws_at->toAtomString(),
				'draw_date' => $draw->draws_at->format('d.m.Y H:i'),
				'prize' => $draw->prize,
				'sold' => $draw->ticket_count,
				'limit' => config('game.gamblinghouse.ticket_limit'),
				'price' => config('game.gamblinghouse.ticket_price'),
				'tickets' => $tickets,
				'pending_tickets' => $pendingTickets,
				'history' => $history,
			];
		}

		return Inertia::render('Map/GamblingHouse', [
			'game' => $game,
			'stakes' => config('game.gamblinghouse.dice_stakes'),
			'dice' => $request->session()->get('gambling_house.dice'),
			'lottery' => $lottery,
		]);
	}

	public function store()
	{
		$request = request();

		$user = $request->user();

		$this->prepareAction($request);

		$data = $request->validate([
			'action' => ['required', Rule::in(['dice', 'ticket'])],
			'stake' => [
				'required_if:action,dice',
				'integer',
				Rule::in(config('game.gamblinghouse.dice_stakes')),
			],
			'draw_id' => ['required_if:action,ticket', 'integer', 'min:1'],
		], [
			'action.required' => 'Выберите игру.',
			'action.in' => 'Выберите доступную игру.',
			'stake.required_if' => 'Выберите ставку.',
			'stake.in' => 'Выберите одну из доступных ставок.',
			'draw_id.required_if' => 'Обновите страницу для покупки билета.',
		]);

		$game = $data['action'] === 'ticket' ? 'lottery' : 'dice';

		try {
			if ($data['action'] === 'dice') {
				$result = GamblingHouseService::rollDice($user, (int) $data['stake']);

				session()->put('gambling_house.dice', $result);
			} else {
				$ticket = GamblingHouseService::buyTicket($user, (int) $data['draw_id']);

				flash('Вы купили билет № ' . $ticket->number . '. Удачи в розыгрыше!');
			}
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['game' => $game]);
	}
}
