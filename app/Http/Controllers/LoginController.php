<?php

namespace App\Http\Controllers;

use App\Engine\Services\UserService;
use App\Http\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UserAuthentication;
use App\Support\SocialLoginProviders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
	public function index(): RedirectResponse
	{
		return redirect()->to(route('index') . '#login');
	}

	public function store(LoginRequest $request): RedirectResponse
	{
		$data = $request->validated();

		$credentials = [
			'email' => $data['email'],
			'password' => $data['password'],
			'is_clone' => false,
		];
		$canLogin = fn (User $user) => $user->blocked_at === null || !$user->blocked_at->isFuture();

		if (!Auth::attemptWhen($credentials, $canLogin, $request->boolean('remember'))) {
			throw ValidationException::withMessages([
				'email' => 'Не удалось войти. Проверьте почту и пароль. Если доступ к персонажу ограничен, обратитесь к администрации.',
			])->redirectTo(route('index') . '#login');
		}

		$request->session()->regenerate();

		return redirect()->route('person.detail');
	}

	public function logout(Request $request): Response
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return Inertia::location(route('index'));
	}

	public function services(string $service): RedirectResponse
	{
		if (!array_key_exists($service, SocialLoginProviders::available())) {
			return redirect()->route('index');
		}

		return Socialite::driver($service)->redirect();
	}

	public function callback(Request $request, string $service): RedirectResponse
	{
		if (!array_key_exists($service, SocialLoginProviders::available())) {
			return redirect()->route('index');
		}

		try {
			$profile = Socialite::driver($service)->user();
		} catch (\Exception) {
			return redirect()->to(route('index') . '#login')->withErrors([
				'email' => 'Не удалось войти через VK ID. Попробуйте ещё раз.',
			]);
		}

		$authData = UserAuthentication::query()->where('provider', $service)
			->where('provider_id', $profile->getId())->first();

		if ($authData) {
			$user = $authData->user;
		} else {
			$user = DB::transaction(function () use ($profile, $service) {
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
		}

		if ($user === null || $user->blocked_at?->isFuture() || $user->is_clone) {
			return redirect()->to(route('index') . '#login')->withErrors([
				'email' => 'Доступ к персонажу ограничен. Обратитесь к администрации.',
			]);
		}

		if ($authData) {
			$authData->login_at = now();
			$authData->save();
		}

		Auth::login($user, true);
		$request->session()->regenerate();

		if ($user->gender === null) {
			return redirect()->route('person.settings');
		}

		return redirect()->route('person.detail');
	}
}
