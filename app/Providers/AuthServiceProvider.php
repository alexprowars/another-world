<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\VKID;

class AuthServiceProvider extends ServiceProvider
{
	public function boot(Gate $gate): void
	{
		$gate->before(function ($user) {
			return $user->id === 1;
		});

		Authenticate::redirectUsing(function () {
			return '/';
		});
	}

	public function register()
	{
		Event::listen(function (SocialiteWasCalled $event) {
			$event->extendSocialite('vkid', VKID\Provider::class);
		});
	}
}
