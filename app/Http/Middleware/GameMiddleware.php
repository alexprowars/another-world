<?php

namespace App\Http\Middleware;

use App\Http\Controllers\BattleController;
use App\Http\Controllers\MapController;
use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GameMiddleware
{
	public function handle(Request $request, Closure $next): Response
	{
		$user = $request->user();

		$hp = UserService::getCuredHealth($user);

		$user->online = now();
		$user->hp_now += $hp;

		if ($user->online->diffInSeconds() < 15) {
			$user->online = now();
			$user->save();
		}

		if (($user->r_date && !$user->r_type) || (!$user->r_date && $user->r_type != 0)) {
			$user->r_date = null;
			$user->r_type = null;
			$user->save();
		}

		$dispatch = null;

		if ($user->battle_id) {
			$dispatch = BattleController::class;
		} elseif ($user->r_date && $user->r_type) {
			switch ($user->r_type) {
				case 1:
					UserService::checkRoom($user, 666);
					$dispatch = MapController::class;
					break;
				case 2:
					UserService::checkRoom($user, 8);
					$dispatch = MapController::class;
					break;
				case 3:
					UserService::checkRoom($user, 9);
					$dispatch = MapController::class;
					break;
				case 4:
					UserService::checkRoom($user, 16);
					$dispatch = MapController::class;
					break;
				case 7:
					UserService::checkRoom($user, 11);
					$dispatch = MapController::class;
					break;
				case 10:
					$dispatch = MapController::class;
					break;
			}
		}

		if ($dispatch) {
			$controller = $request->route()->getController();

			if ($controller && get_class($controller) !== $dispatch) {
				return redirect()->action([$dispatch, 'index']);
			}
		}

		$user->rating = UserService::getUserRaiting($user);
		$user->calculate();

		return $next($request);
	}
}
