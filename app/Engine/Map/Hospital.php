<?php

namespace App\Engine\Map;

use App\Models\User;
use App\Services\ChatService;
use Inertia\Inertia;

class Hospital
{
	public function __invoke()
	{
		$user = auth()->user();
		$time = round((1 - ($user->hp_now / $user->hp_max)) * $this->getHealingTime($user));

		if (request()->has('heal') && !$user->r_date && $user->vitality > 0 && $time) {
			$user->r_date = now()->addSeconds($time);
			$user->r_type = 2;
			$user->update();
		}

		// Лечение травмы
		if (request()->has('injury') && $user->injury?->isFuture() && $user->gold >= 200) {
			$user->effects()->where('type', 3)->delete();

			$user->update([
				'injury' => null,
				'injury_type' => null,
				'gold' => $user->gold - 200,
				'room' => 1,
			]);

			ChatService::sendSystemMessage($user, '', 'Лечение окончено! Вы транспортированы в помещение: <b><u>Общий зал</u></b>');

			return to_route('map');
		}

		if ($user->r_date) {
			$this->checkHealing($user);

			$time = max(0, (int) now()->diffInSeconds($user->r_date));

			if ($time <= 0) {
				return to_route('map');
			}
		}

		return Inertia::render('Map/Hospital', [
			'time' => $time,
		]);
	}

	protected function getHealingTime(User $user)
	{
		if ($user->level < 4) {
			return 180;
		} else {
			return 360;
		}
	}

	protected function checkHealing(User $user)
	{
		if (!$user->r_date) {
			return;
		}

		$remainingSeconds = max(0, (int) now()->diffInSeconds($user->r_date));

		if ($user->vitality < 1) {
			$user->update(['r_date' => null, 'r_type' => null]);
		}

		if ($remainingSeconds <= 0) {
			$user->update([
				'r_date' => null,
				'r_type' => null,
				'room' => 1,
				'hp_now' => $user->hp_max,
			]);

			ChatService::sendSystemMessage($user, '', 'Лечение окончено! Вы транспортированы в помещение: <b><u>Общий зал</u></b>');
		}

		$hp = $user->hp_max - round($remainingSeconds * ($user->hp_max / $this->getHealingTime($user)));
		$user->hp_now = max(0, min($user->hp_max, $hp));
		$user->save();
	}
}
