<?php

namespace App\Http\Middleware;

use App\Engine\World\Location;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocation
{
	public function handle(Request $request, Closure $next): Response
	{
		$code = $request->route('city') . '.' . $request->route('locationCode');

		if ($request->route('vaultRoom') !== null) {
			$code .= '.' . $request->route('vaultRoom');
		}

		$location = Location::fromCode($code);
		$location->ensureExists();
		$user = $request->user();

		if ($request->isMethod('get')) {
			if ($user->location !== $location->value()) {
				return redirect($user->currentLocation()->url());
			}

			return $next($request);
		}

		return DB::transaction(function () use ($request, $next, $location, $user) {
			$user->refreshForUpdate();

			abort_if($user->trashed(), 404);

			if ($user->location !== $location->value()) {
				throw ValidationException::withMessages([
					'location' => 'Персонаж уже находится в другой локации. Обновите страницу.',
				]);
			}

			return $next($request);
		}, 3);
	}
}
