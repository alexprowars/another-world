<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\User;
use App\Models\UserAuthentication;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
	private array $socialDrivers = [
		'vkid',
	];

	public function index()
	{
	}

	public function services(string $service): RedirectResponse
	{
		if (!in_array($service, $this->socialDrivers)) {
			return redirect()->away('/');
		}

		return Socialite::driver($service)->redirect();
	}

	public function callback(string $service): RedirectResponse
	{
		if (!in_array($service, $this->socialDrivers)) {
			return redirect()->away('/');
		}

		try {
			$profile = Socialite::driver($service)->user();
		} catch (\Exception) {
			return redirect()->away('/');
		}

		$authData = UserAuthentication::query()->where('provider', $service)
			->where('provider_id', $profile->getId())->first();

		if ($authData) {
			$authData->login_at = now();
			$authData->save();

			Auth::loginUsingId($authData->user_id, true);
		} else {
			$user = DB::transaction(static function () use ($profile, $service) {
				$email = $profile->getEmail();

				if (empty($email)) {
					$email = 'social@' . $profile->getId();
				}

				$user = User::query()->where('email', $email)
					->lockForUpdate()->first();

				if (!$user) {
					$user = UserService::creation([
						'name' => $profile->getNickname() ?: $profile->getName(),
						'email' => $email,
					], true);
				}

				$user->authentications()->create([
					'provider'		=> $service,
					'provider_id' 	=> $profile->getId(),
					'login_at' 		=> now(),
				]);

				return $user;
			});

			Auth::login($user, true);
		}

		return redirect()->away('/game');
	}
}
