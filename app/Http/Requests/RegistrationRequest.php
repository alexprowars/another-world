<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
	/** @return array<string, list<string>> */
	public function rules(): array
	{
		return [
			'name' => [
				'required',
				'string',
				'min:3',
				'max:100',
				'regex:/\A[a-zA-Zа-яА-ЯёЁ0-9_.,!?* -]+\z/u',
				'unique:users,name',
			],
			'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
			'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'],
			'password_confirmation' => ['required', 'string'],
			'gender' => ['required', 'in:M,F'],
			'terms' => ['accepted'],
		];
	}

	/** @return array<string, string> */
	public function messages(): array
	{
		return [
			'name.required' => 'Введите имя персонажа.',
			'name.min' => 'Имя персонажа должно содержать не менее 3 символов.',
			'name.max' => 'Имя персонажа не должно быть длиннее 100 символов.',
			'name.regex' => 'Имя персонажа содержит запрещённые символы.',
			'name.unique' => 'Это имя персонажа уже занято.',
			'email.required' => 'Введите электронную почту.',
			'email.email' => 'Введите корректную электронную почту.',
			'email.max' => 'Адрес почты не должен быть длиннее 50 символов.',
			'email.unique' => 'Эта почта уже используется.',
			'password.required' => 'Введите пароль.',
			'password.min' => 'Пароль должен содержать не менее 6 символов.',
			'password.max' => 'Пароль не должен быть длиннее 72 символов.',
			'password.confirmed' => 'Пароли не совпадают.',
			'password_confirmation.required' => 'Повторите пароль.',
			'gender.required' => 'Выберите пол персонажа.',
			'gender.in' => 'Выберите пол персонажа из списка.',
			'terms.accepted' => 'Примите законы игры и пользовательское соглашение.',
		];
	}

	protected function getRedirectUrl(): string
	{
		return route('register');
	}
}
