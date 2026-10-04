<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
	/** @return array<string, list<string>> */
	public function rules(): array
	{
		return [
			'token' => ['required', 'string', 'size:64'],
			'email' => ['required', 'string', 'email', 'max:50'],
			'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'],
			'password_confirmation' => ['required', 'string'],
		];
	}

	/** @return array<string, string> */
	public function messages(): array
	{
		return [
			'token.required' => 'Запросите новую ссылку для восстановления пароля.',
			'token.string' => 'Ссылка для восстановления пароля недействительна.',
			'token.size' => 'Ссылка для восстановления пароля недействительна.',
			'email.required' => 'Введите электронную почту.',
			'email.string' => 'Введите корректную электронную почту.',
			'email.email' => 'Введите корректную электронную почту.',
			'email.max' => 'Адрес почты не должен быть длиннее 50 символов.',
			'password.required' => 'Введите новый пароль.',
			'password.string' => 'Введите корректный пароль.',
			'password.min' => 'Пароль должен содержать не менее 6 символов.',
			'password.max' => 'Пароль не должен быть длиннее 72 символов.',
			'password.confirmed' => 'Пароли не совпадают.',
			'password_confirmation.required' => 'Повторите пароль.',
			'password_confirmation.string' => 'Введите корректное подтверждение пароля.',
		];
	}
}
