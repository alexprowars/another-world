<?php

namespace App\Http\Middleware;

use App\Engine\Battle\Enums\BattleStatus;
use App\Http\Controllers\ArenaController;
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

		if (($user->r_date && !$user->r_type) || (!$user->r_date && $user->r_type != 0)) {
			$user->r_date = null;
			$user->r_type = null;
			$user->save();
		}

		$dispatch = null;

		if ($user->battle_id && !str_contains($request->route()->uri(), 'chat/')) {
			$dispatch = in_array($user->battle?->status, [BattleStatus::ACTIVE, BattleStatus::FINISHED], true)
				? BattleController::class
				: ArenaController::class;
		} elseif ($user->r_date) {
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
				case 8:
				case 10:
					$dispatch = MapController::class;
					break;
			}
		} elseif ($user->prison) {
			UserService::checkRoom($user, 666);
			$dispatch = MapController::class;
		}

		if ($dispatch) {
			$controller = $request->route()->getController();

			if ($controller && get_class($controller) !== $dispatch) {
				return redirect()->action([$dispatch, 'index']);
			}
		}

		$user->rating = UserService::getUserRaiting($user);
		$user->calculate();

		if ($user->online === null || $user->online->diffInSeconds() >= 15) {
			$hp = UserService::getCuredHealth($user);

			$user->online = now();
			$user->hp_now += $hp;
			$user->save();
		}

		return $next($request);
	}
}
