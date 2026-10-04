<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferGoldRequest extends FormRequest
{
	/** @return array<string, list<string>> */
	public function rules(): array
	{
		return [
			'recipient_id' => ['required', 'integer', 'min:1'],
			'amount' => ['required', 'regex:/^\d{1,10}([.,]\d{1,2})?$/'],
			'comment' => ['required', 'string', 'max:255'],
		];
	}

	/** @return array<string, string> */
	public function messages(): array
	{
		return [
			'amount.required' => 'Укажите сумму.',
			'amount.regex' => 'Укажите сумму с точностью до сотых.',
			'comment.required' => 'Укажите причину передачи.',
			'comment.max' => 'Причина должна содержать не более 255 символов.',
		];
	}
}
