<?php

namespace App\Http;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	protected ?User $user = null;

	public function __construct()
	{
		$this->middleware(function (Request $request, Closure $next) {
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
	}
}
