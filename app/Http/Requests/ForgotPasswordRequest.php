<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
	/** @return array<string, list<string>> */
	public function rules(): array
	{
		return [
			'email' => ['required', 'string', 'email', 'max:50'],
		];
	}

	/** @return array<string, string> */
	public function messages(): array
	{
		return [
			'email.required' => 'Введите электронную почту.',
			'email.string' => 'Введите корректную электронную почту.',
			'email.email' => 'Введите корректную электронную почту.',
			'email.max' => 'Адрес почты не должен быть длиннее 50 символов.',
		];
	}

	protected function getRedirectUrl(): string
	{
		return route('password.request');
	}
}
