<?php

namespace App\Engine\Locations;

use App\Engine\Services\GamblingHouseService;
use App\Exceptions\Exception;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class GamblingHouseController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.gambling-house.dicePage', ['city' => $this->user->currentLocation()->city]);
	}

	public function dicePage(): Response
	{
		return Inertia::render('Map/GamblingHouse', [
			'game' => 'dice',
			'stakes' => config('game.gamblinghouse.dice_stakes'),
			'dice' => session()->get('gambling_house.dice'),
		]);
	}

	public function lotteryPage(): Response
	{
		$user = $this->user;

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

		return Inertia::render('Map/GamblingHouse', [
			'game' => 'lottery',
			'lottery' => $lottery,
		]);
	}

	public function dice(Request $request)
	{
		$data = $request->validate([
			'stake' => [
				'required',
				'integer',
				Rule::in(config('game.gamblinghouse.dice_stakes')),
			],
		], [
			'stake.required' => 'Выберите ставку.',
			'stake.in' => 'Выберите одну из доступных ставок.',
		]);

		try {
			$result = GamblingHouseService::rollDice($request->user(), (int) $data['stake']);

			session()->put('gambling_house.dice', $result);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.gambling-house.dicePage', ['city' => $this->user->currentLocation()->city]);
	}

	public function ticket(Request $request)
	{
		$data = $request->validate([
			'draw_id' => ['required', 'integer', 'min:1'],
		], [
			'draw_id.required' => 'Обновите страницу для покупки билета.',
		]);

		try {
			$ticket = GamblingHouseService::buyTicket($request->user(), (int) $data['draw_id']);

			flash('Вы купили билет № ' . $ticket->number . '. Удачи в розыгрыше!');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.gambling-house.lotteryPage', ['city' => $this->user->currentLocation()->city]);
	}
}
