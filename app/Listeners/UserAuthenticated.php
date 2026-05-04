<?php

namespace App\Listeners;

use App\Exceptions\Exception;
use App\Models\Blocked;
use App\Models\User;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class UserAuthenticated
{
	public function handle(Authenticated $event): void
	{
		$route = Route::current()->getName();

		if ($route === 'banned') {
			return;
		}

		/** @var User $user */
		$user = $event->user;

		if ($user->blocked_at) {
			if ($user->blocked_at->isFuture()) {
				throw new Exception('Ваш аккаунт заблокирован. Срок окончания блокировки: ' . $game->datezone("d.m.Y H:i:s", $user->blocked_at) . '<br>Для получения дополнительной информации зайдите <a href="' . URL::to('/banned') . '">сюда</a>');
			} else {
				$user->blocked_at = null;
				$user->save();

				Blocked::query()->whereBelongsTo($user)->delete();
			}
		}
	}
}
