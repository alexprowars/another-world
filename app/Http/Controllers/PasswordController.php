<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PasswordController extends Controller
{
	public function request(): View
	{
		return view('index.forgot_password');
	}

	public function email(ForgotPasswordRequest $request): RedirectResponse
	{
		$data = $request->validated();

		Password::sendResetLink([
			'email' => $data['email'],
			'is_clone' => false,
		]);

		return redirect()->route('password.request')
			->withInput(['email' => $data['email']])
			->with('status', 'Если эта почта зарегистрирована, на неё отправлена ссылка для смены пароля. Повторный запрос доступен через минуту.');
	}

	public function reset(Request $request, string $token): View
	{
		$email = $request->query('email');

		return view('index.reset_password', [
			'token' => $token,
			'email' => is_string($email) ? $email : '',
		]);
	}

	public function update(ResetPasswordRequest $request): RedirectResponse
	{
		$data = $request->validated();

		$status = Password::reset([
			'email' => $data['email'],
			'password' => $data['password'],
			'token' => $data['token'],
			'is_clone' => false,
		], function (User $user, string $password) {
			$user->password = Hash::make($password);
			$user->remember_token = Str::random(60);
			$user->save();

			event(new PasswordReset($user));
		});

		if ($status !== Password::PASSWORD_RESET) {
			throw ValidationException::withMessages([
				'email' => 'Ссылка недействительна или срок её действия истёк. Запросите новую ссылку для восстановления пароля.',
			])->redirectTo(route('password.reset', [
				'token' => $data['token'],
				'email' => $data['email'],
			]));
		}

		return redirect()->to(route('index') . '#login')
			->with('status', 'Пароль изменён. Войдите в игру с новым паролем.');
	}
}
