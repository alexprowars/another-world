<?php

namespace App\Http\Middleware;

use App\Models\LogsIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserIP
{
	public function handle(Request $request, Closure $next): Response
	{
		$response = $next($request);

		$ip = $request->ip();

		if (
			($user = $request->user())
			&& filter_var($ip, FILTER_VALIDATE_IP) !== false
			&& $user->ip !== $ip
			&& !in_array($ip, ['127.0.0.1', '::1'], true)
		) {
			$user->ip = $ip;
			$user->save();

			$log = new LogsIp();
			$log->ip = $ip;
			$log->user()->associate($user);
			$log->save();
		}

		return $response;
	}
}
