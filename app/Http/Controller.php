<?php

namespace App\Http;

use App\Http\Controllers\IndexController;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	public function __construct()
	{
		$this->middleware(function (Request $request, Closure $next) {
			$controller = $request->route()->getController();

			if ($request->user() && $controller::class == IndexController::class) {
				return redirect()->route('game');
			}

			$this->init($request->user());

			return $next($request);
		});
	}

	private function init(?User $user): void
	{
	}
}
