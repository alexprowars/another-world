<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
	/** @return array<string, list<string>> */
	public function rules(): array
	{
		return [
			'email' => ['required', 'string', 'email', 'max:50'],
			'password' => ['required', 'string', 'max:255'],
			'remember' => ['sometimes', 'boolean'],
		];
	}
}
