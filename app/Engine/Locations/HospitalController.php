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

		if ($this->checkHealing($user)) {
			return $this->redirectToLocation();
		}

		$canHeal = $user->getCombatStats()->vitality > 0 && $user->hp_max > 0;
		$time = $canHeal ? $this->getRecoveryTime(
			$user,
			UserService::getHospitalHealingTime(),
		) : 0;

		if ($user->r_date) {
			$time = max(0, (int) ceil(now()->diffInSeconds($user->r_date)));
		}

		return Inertia::render('Map/Hospital', [
			'can_heal' => $canHeal,
			'time' => $time,
			'natural_time' => $canHeal ? $this->getRecoveryTime(
				$user,
				config('game.regeneration.health_time'),
			) : 0,
		]);
	}

	public function heal()
	{
		$user = $this->user;

		if (
			!$user->r_date
			&& !$user->r_type
			&& !$user->battle_id
			&& $user->getCombatStats()->vitality > 0
			&& $user->hp_max > 0
		) {
			$user->hp_now += UserService::getCuredHealth($user);

			$time = (int) ceil((1 - $user->hp_now / $user->hp_max) * UserService::getHospitalHealingTime());

			if ($time > 0) {
				$user->r_date = now()->addSeconds($time);
				$user->r_type = 2;
			}

			$user->save();
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

	protected function checkHealing(User $user): bool
	{
		if ($user->r_type != 2 || !$user->r_date) {
			return false;
		}

		if ($user->getCombatStats()->vitality <= 0 || $user->hp_max <= 0) {
			$user->update(['r_date' => null, 'r_type' => null]);

			return true;
		}

		if ($user->r_date->isFuture()) {
			$remaining = now()->diffInSeconds($user->r_date);
			$health = $user->hp_max - $remaining * $user->hp_max / UserService::getHospitalHealingTime();
			$user->hp_now = max(0, min($user->hp_max, round($health, 4)));
			$user->save();

			return false;
		}

		$user->update([
			'r_date' => null,
			'r_type' => null,
			'location' => $user->currentLocation()->inCity('arena')->value(),
			'hp_now' => $user->hp_max,
		]);

		ChatService::sendSystemMessage('Лечение окончено! Вы транспортированы в помещение: Общий зал', [$user]);

		return true;
	}

	private function getRecoveryTime(User $user, int $duration): int
	{
		$health = $user->hp_now + UserService::getCuredHealth($user);

		return (int) ceil(max(0, (1 - $health / $user->hp_max) * $duration));
	}
}
