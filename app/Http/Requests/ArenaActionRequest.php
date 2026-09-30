<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArenaActionRequest extends FormRequest
{
	/** @return array<string, list<string>> */
	public function rules(): array
	{
		return [
			'action' => ['required', 'in:create,take,withdraw,dismiss,start,teleport'],
			'battle_type' => ['sometimes', 'integer', 'in:1,2,3'],
			'offer' => ['required_if:action,take', 'integer', 'min:1'],
			'battle_side' => ['sometimes', 'integer', 'in:0,1'],
			'timeout' => ['sometimes', 'integer', 'in:1,3,5,10'],
			'comment' => ['nullable', 'string', 'max:255'],
			'offer_level' => ['sometimes', 'integer', 'in:1,2,3,4'],
			'time_battle_start' => ['sometimes', 'integer', 'in:180,300,600,900'],
			'capacity' => ['sometimes', 'integer', 'between:2,25'],
			'blood' => ['sometimes', 'boolean'],
			'unarmed' => ['sometimes', 'boolean'],
		];
	}
}
