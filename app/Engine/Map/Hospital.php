<?php

namespace App\Engine\Map;

use App\Models\User;
use App\Services\ChatService;
use App\Services\UserService;
use Inertia\Inertia;

class Hospital
{
	public function __invoke()
	{
		$user = auth()->user();
		$canHeal = $user->vitality > 0 && $user->hp_max > 0;
		$time = 0;

		if ($canHeal) {
			$time = round((1 - ($user->hp_now / $user->hp_max)) * UserService::getHospitalHealingTime($user));
		}

		if (request()->has('heal') && !$user->r_date && $canHeal && $time > 0) {
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

			ChatService::sendSystemMessage('Лечение окончено! Вы транспортированы в помещение: Общий зал', [$user]);

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
			'can_heal' => $canHeal,
			'time' => $time,
		]);
	}

	protected function checkHealing(User $user)
	{
		if (!$user->r_date) {
			return;
		}

		if ($user->vitality <= 0 || $user->hp_max <= 0) {
			$user->update(['r_date' => null, 'r_type' => null]);

			return;
		}

		$remainingSeconds = max(0, (int) now()->diffInSeconds($user->r_date));

		if ($remainingSeconds <= 0) {
			$user->update([
				'r_date' => null,
				'r_type' => null,
				'room' => 1,
				'hp_now' => $user->hp_max,
			]);

			ChatService::sendSystemMessage('Лечение окончено! Вы транспортированы в помещение: Общий зал', [$user]);
		}

		$hp = $user->hp_max - round($remainingSeconds * ($user->hp_max / UserService::getHospitalHealingTime($user)));
		$user->hp_now = max(0, min($user->hp_max, $hp));
		$user->save();
	}
}
