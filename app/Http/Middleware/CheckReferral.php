<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckReferral
{
	public function handle(Request $request, Closure $next): Response
	{
		if (Session::has('uid')) {
			return $next($request);
		}

		$id = (int) $request->server('QUERY_STRING', 0);

		if (empty($id)) {
			return $next($request);
		}

		$user = User::findOne($id);

		if (!$user) {
			return $next($request);
		}

		session()->put('ref', $user->id);

		return $next($request);
	}
}
