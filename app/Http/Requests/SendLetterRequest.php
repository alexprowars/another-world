<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendLetterRequest extends FormRequest
{
	/** @return array<string, list<string>> */
	public function rules(): array
	{
		return [
			'recipient' => ['required', 'string', 'max:100'],
			'subject' => ['required', 'string', 'max:100'],
			'body' => ['required', 'string', 'max:5000'],
		];
	}

	/** @return array<string, string> */
	public function messages(): array
	{
		return [
			'recipient.required' => 'Укажите имя получателя.',
			'recipient.max' => 'Имя получателя должно содержать не более 100 символов.',
			'subject.required' => 'Введите тему письма.',
			'subject.max' => 'Тема должна содержать не более 100 символов.',
			'body.required' => 'Введите текст письма.',
			'body.max' => 'Текст письма должен содержать не более 5000 символов.',
		];
	}
}
