<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class GamblingHouseService
{
	/** @return array{player: list<int>, opponent: list<int>, stake: int, outcome: string, change: int} */
	public static function rollDice(User $user, int $stake): array
	{
		if (!in_array($stake, config('game.gamblinghouse.dice_stakes'), true)) {
			throw new Exception('Выберите одну из доступных ставок.');
		}

		return DB::transaction(function () use ($user, $stake) {
			$account = User::query()->lockForUpdate()->findOrFail($user->id);

			self::validatePlayer($account, $stake);

			$player = [random_int(1, 6), random_int(1, 6)];
			$opponent = [random_int(1, 6), random_int(1, 6)];
			$comparison = array_sum($player) <=> array_sum($opponent);
			$change = $comparison * $stake;

			if ($change !== 0) {
				$account->update(['gold' => round($account->gold + $change, 2)]);
			}

			return [
				'player' => $player,
				'opponent' => $opponent,
				'stake' => $stake,
				'outcome' => match ($comparison) {
					1 => 'win',
					-1 => 'loss',
					default => 'draw',
				},
				'change' => $change,
			];
		}, 3);
	}

	public static function currentDraw(): LotteryDraw
	{
		$drawsAt = CarbonImmutable::now()
			->startOfWeek(CarbonImmutable::MONDAY)
			->addWeek();

		return LotteryDraw::query()->firstOrCreate(['draws_at' => $drawsAt]);
	}

	public static function buyTicket(User $user, int $drawId): LotteryTicket
	{
		return DB::transaction(function () use ($user, $drawId) {
			$draw = LotteryDraw::query()->lockForUpdate()->find($drawId);

			if (!$draw || $draw->drawn_at || $draw->draws_at->lessThanOrEqualTo(now())) {
				throw new Exception('Этот розыгрыш уже закрыт. Обновите страницу для покупки билета.');
			}

			if ($draw->ticket_count >= config('game.gamblinghouse.ticket_limit')) {
				throw new Exception('Все билеты проданы. Следующий розыгрыш откроется в понедельник.');
			}

			$account = User::query()->lockForUpdate()->findOrFail($user->id);
			$ticketPrice = config('game.gamblinghouse.ticket_price');

			self::validatePlayer($account, $ticketPrice);

			$ticket = $draw->tickets()->create([
				'user_id' => $account->id,
				'number' => $draw->ticket_count + 1,
			]);

			$account->update(['gold' => round($account->gold - $ticketPrice, 2)]);
			$draw->update([
				'ticket_count' => $draw->ticket_count + 1,
				'prize' => $draw->prize + $ticketPrice,
			]);

			return $ticket;
		}, 3);
	}

	public static function drawDueLotteries(): int
	{
		$count = 0;

		$draws = LotteryDraw::query()
			->whereNull('drawn_at')
			->where('draws_at', '<=', now())
			->orderBy('id')
			->lazyById();

		foreach ($draws as $draw) {
			$completed = DB::transaction(function () use ($draw) {
				$lockedDraw = LotteryDraw::query()->lockForUpdate()->findOrFail($draw->id);

				if ($lockedDraw->drawn_at || $lockedDraw->draws_at->isFuture()) {
					return false;
				}

				if ($lockedDraw->ticket_count > 0) {
					$number = random_int(1, $lockedDraw->ticket_count);
					$ticket = $lockedDraw->tickets()->where('number', $number)->firstOrFail();
					$winner = User::withTrashed()->lockForUpdate()->findOrFail($ticket->user_id);

					$winner->update(['gold' => round($winner->gold + $lockedDraw->prize, 2)]);
					$lockedDraw->fill([
						'winner_id' => $winner->id,
						'winner_name' => $winner->name,
						'winning_number' => $number,
					]);
				}

				$lockedDraw->drawn_at = now();
				$lockedDraw->save();

				return true;
			}, 3);

			if ($completed) {
				$count++;
			}
		}

		return $count;
	}

	private static function validatePlayer(User $user, int $amount): void
	{
		if ($user->room != 12 || $user->battle_id || $user->r_date || $user->prison?->isFuture()) {
			throw new Exception('Играть можно только в игорном доме, вне боя и работы.');
		}

		if ($user->gold < $amount) {
			throw new Exception('Недостаточно золота.');
		}
	}
}
