<?php

namespace App\Http\Controllers;

use App\Engine\Services\UserService;
use App\Http\Controller;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends Controller
{
	public function index(): View
	{
		return view('index.register');
	}

	public function store(RegistrationRequest $request): RedirectResponse
	{
		$data = $request->validated();

		try {
			$user = DB::transaction(fn () => UserService::creation([
				'name' => $data['name'],
				'email' => $data['email'],
				'password' => $data['password'],
				'gender' => $data['gender'],
			]));
		} catch (UniqueConstraintViolationException) {
			throw ValidationException::withMessages([
				'email' => 'Эта почта уже используется.',
			])->redirectTo(route('register'));
		}

		Auth::login($user);
		$request->session()->regenerate();

		return redirect()->route('person.detail');
	}
}
