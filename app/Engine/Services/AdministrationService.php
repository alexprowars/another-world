<?php

namespace App\Engine\Services;

use App\Exceptions\Exception;
use App\Models\TribeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdministrationService
{
	public static function submitRequest(User $user): void
	{
		DB::transaction(function () use ($user) {
			$account = User::query()->lockForUpdate()->findOrFail($user->id);

			if (TribeRequest::query()->whereBelongsTo($account)->exists()) {
				throw new Exception('Заявка была подана ранее!');
			}

			if ($account->credits < 100) {
				throw new Exception('Недостаточно платины!');
			}

			if ($account->level < 4) {
				throw new Exception('Для подачи заявки вы должны достигнуть 4 уровня в игре!');
			}

			TribeRequest::create([
				'user_id' => $account->id,
				'status' => 0,
			]);

			$account->decrement('credits', 100);
		}, 3);
	}

	public static function withdrawRequest(User $user): void
	{
		DB::transaction(function () use ($user) {
			$account = User::query()->lockForUpdate()->findOrFail($user->id);

			TribeRequest::query()->whereBelongsTo($account)->delete();
		}, 3);
	}

	public static function buyImage(User $user, int $image): void
	{
		if ($image < 1 || $image > 49) {
			throw new Exception('Выбранный образ недоступен!');
		}

		DB::transaction(function () use ($user, $image) {
			$account = User::query()->lockForUpdate()->findOrFail($user->id);
			$path = 'images/' . ($account->gender === 'F' ? 2 : 1) . '/' . $image . '.jpg';

			if ($account->image === $path) {
				throw new Exception('Этот образ уже установлен!');
			}

			if ($account->credits < 2000) {
				throw new Exception('Недостаточно платины!');
			}

			$account->update([
				'image' => $path,
				'credits' => $account->credits - 2000,
			]);
		}, 3);
	}
}
