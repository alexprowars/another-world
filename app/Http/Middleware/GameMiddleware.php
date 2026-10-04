<?php

namespace App\Http\Middleware;

use App\Engine\Battle\Enums\BattleStatus;
use App\Engine\Services\MovementService;
use App\Engine\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class GameMiddleware
{
	public function handle(Request $request, Closure $next): Response
	{
		$user = $request->user();

		if (($user->r_date && !$user->r_type) || (!$user->r_date && $user->r_type != 0)) {
			$user->r_date = null;
			$user->r_type = null;
			$user->save();
		}

		MovementService::finishTravel($user);

		$forcedRoute = null;
		$forcedLocation = null;

		if ($user->battle_id) {
			$forcedRoute = in_array($user->battle?->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)
				? 'battle'
				: 'arena';
		} elseif ($user->r_date) {
			$code = match ($user->r_type) {
				1 => 'prison',
				2 => 'hospital',
				3 => 'academy',
				4 => 'works',
				7 => 'smithy',
				default => null,
			};

			if ($code !== null) {
				UserService::checkLocation($user, $user->currentLocation()->inCity($code)->value());
			}

			$forcedLocation = $user->currentLocation();
		} elseif ($user->prison) {
			UserService::checkLocation($user, $user->currentLocation()->inCity('prison')->value());
			$forcedLocation = $user->currentLocation();
		}

		$redirectUrl = null;

		if (!$request->routeIs('world', 'city')) {
			if ($forcedRoute && !$request->routeIs($forcedRoute, $forcedRoute . '.store')) {
				$redirectUrl = route($forcedRoute);
			} elseif ($forcedLocation) {
				$requestedLocation = $request->route('city') . '.' . $request->route('locationCode');

				if ($request->route('vaultRoom')) {
					$requestedLocation .= '.' . $request->route('vaultRoom');
				}

				if ($requestedLocation !== $forcedLocation->value()) {
					$redirectUrl = $forcedLocation->url();
				}
			}
		}

		if ($redirectUrl !== null) {
			if (!$request->isMethod('get')) {
				throw ValidationException::withMessages([
					'location' => 'Сейчас персонаж занят. Вернитесь к текущему действию.',
				]);
			}

			return redirect($redirectUrl);
		}

		$user->rating = UserService::getUserRaiting($user);
		$user->calculate();

		if ($user->online === null || $user->online->diffInSeconds() >= 15) {
			$hp = UserService::getCuredHealth($user);
			$energy = UserService::getCuredEnergy($user);

			$user->online = now();
			$user->hp_now += $hp;
			$user->energy_now += $energy;
			$user->save();
		}

		return $next($request);
	}
}
