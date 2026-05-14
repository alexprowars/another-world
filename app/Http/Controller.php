<?php

namespace App\Http;

use App\Http\Controllers\IndexController;
use App\Models\User;
use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	protected ?User $user = null;

	public function __construct()
	{
		$this->middleware(function (Request $request, Closure $next) {
			$controller = $request->route()->getController();

			if ($request->user() && $controller::class == IndexController::class) {
				return redirect()->route('person.detail');
			}

			$this->init($request->user());

			return $next($request);
		});
	}

	private function init(?User $user): void
	{
		if (!$user) {
			return;
		}

		$this->user = $user;

		$hp = UserService::getCuredHealth($user);

		$user->onlinetime = now();
		$user->hp_now += $hp;

		if ($user->onlinetime->diffInSeconds() < 15) {
			$user->onlinetime = now();
			$user->save();
		}
	}
}
