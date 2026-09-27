<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BankService
{
	public const int EXCHANGE_RATE = 20;

	public static function donate(User $user, float $amount, ?string $comment = null): Donation
	{
		self::validateAmount($amount);

		return DB::transaction(function () use ($user, $amount, $comment) {
			$account = User::query()->lockForUpdate()->findOrFail($user->id);

			if ($account->gold < $amount) {
				throw new Exception('Недостаточно золота для пожертвования!');
			}

			$account->update(['gold' => round($account->gold - $amount, 2)]);

			return Donation::create([
				'user_id' => $account->id,
				'amount' => $amount,
				'comment' => $comment,
			]);
		}, 3);
	}

	public static function exchange(User $user, float $amount): float
	{
		self::validateAmount($amount);

		return DB::transaction(function () use ($user, $amount) {
			$account = User::query()->lockForUpdate()->findOrFail($user->id);

			if ($account->credits < $amount) {
				throw new Exception('Недостаточно платины для обмена!');
			}

			$gold = round($amount * self::EXCHANGE_RATE, 2);

			$account->update([
				'credits' => round($account->credits - $amount, 2),
				'gold' => round($account->gold + $gold, 2),
			]);

			return $gold;
		}, 3);
	}

	private static function validateAmount(float $amount): void
	{
		if (!is_finite($amount) || $amount <= 0 || $amount > 9999999999.99 || round($amount, 2) != $amount) {
			throw new Exception('Укажите положительную сумму с точностью до сотых.');
		}
	}
}