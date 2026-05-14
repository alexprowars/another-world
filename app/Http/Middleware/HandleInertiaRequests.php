<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
	/**
	 * @return array<string, mixed>
	 */
	public function share(Request $request): array
	{
		$state = [
			'locale' => 'ru',
			'user' => null,
		];

		if ($user = $request->user()) {
			$state['user'] = fn () => UserResource::make($user);
		}

		return [
			...parent::share($request),
			'state' => $state,
		];
	}
}
