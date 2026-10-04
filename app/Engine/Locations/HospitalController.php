<?php

namespace App\Engine\Locations;

use App\Engine\Services\ChatService;
use App\Engine\Services\UserService;
use App\Models\User;
use Inertia\Inertia;

class HospitalController extends LocationController
{
	public function index()
	{
		$user = auth()->user();
		$canHeal = $user->getCombatStats()->vitality > 0 && $user->hp_max > 0;
		$time = 0;

		if ($canHeal) {
			$time = round((1 - ($user->hp_now / $user->hp_max)) * UserService::getHospitalHealingTime($user));
		}

		if ($user->r_date) {
			$this->checkHealing($user);

			$time = max(0, (int) now()->diffInSeconds($user->r_date));

			if ($time <= 0) {
				return $this->redirectToLocation();
			}
		}

		return Inertia::render('Map/Hospital', [
			'can_heal' => $canHeal,
			'time' => $time,
		]);
	}

	public function heal()
	{
		$user = $this->user;

		if (
			!$user->r_date
			&& !$user->r_type
			&& $user->getCombatStats()->vitality > 0
			&& $user->hp_max > 0
		) {
			$time = round((1 - ($user->hp_now / $user->hp_max)) * UserService::getHospitalHealingTime($user));

			if ($time > 0) {
				$user->update([
					'r_date' => now()->addSeconds($time),
					'r_type' => 2,
				]);
			}
		}

		return $this->redirectToLocation();
	}

	public function injury()
	{
		$user = $this->user;

		if ($user->injury?->isFuture() && $user->gold >= 200) {
			$user->effects()->where('type', 3)->delete();
			$user->unsetRelation('effects');

			$user->update([
				'injury' => null,
				'injury_type' => null,
				'gold' => $user->gold - 200,
				'location' => $user->currentLocation()->inCity('arena')->value(),
			]);

			ChatService::sendSystemMessage('Лечение окончено! Вы транспортированы в помещение: Общий зал', [$user]);
		}

		return $this->redirectToLocation();
	}

	protected function checkHealing(User $user)
	{
		if (!$user->r_date) {
			return;
		}

		if ($user->getCombatStats()->vitality <= 0 || $user->hp_max <= 0) {
			$user->update(['r_date' => null, 'r_type' => null]);

			return;
		}

		$remainingSeconds = max(0, (int) now()->diffInSeconds($user->r_date));

		if ($remainingSeconds <= 0) {
			$user->update([
				'r_date' => null,
				'r_type' => null,
				'location' => $user->currentLocation()->inCity('arena')->value(),
				'hp_now' => $user->hp_max,
			]);

			ChatService::sendSystemMessage('Лечение окончено! Вы транспортированы в помещение: Общий зал', [$user]);
		}

		$hp = $user->hp_max - round($remainingSeconds * ($user->hp_max / UserService::getHospitalHealingTime($user)));
		$user->hp_now = max(0, min($user->hp_max, $hp));
		$user->save();
	}
}
