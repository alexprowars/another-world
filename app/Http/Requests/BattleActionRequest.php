<?php

namespace App\Http\Requests;

use App\Engine\Battle\Data\TurnData;
use Illuminate\Foundation\Http\FormRequest;

class BattleActionRequest extends FormRequest
{
	private const array TURN_ZONES = [
		'head' => 1,
		'case' => 2,
		'stomach' => 3,
		'belt' => 4,
		'legs' => 5,
	];

	/** @return array<string, list<string>> */
	public function rules(): array
	{
		$rules = [
			'round' => ['sometimes', 'integer', 'min:0'],
			'opponent' => ['sometimes', 'integer', 'min:0'],
			'lastLogId' => ['sometimes', 'integer', 'min:0'],
			'ability' => ['sometimes', 'integer', 'min:1'],
		];

		foreach (array_keys(self::TURN_ZONES) as $field) {
			$rules[$field . 'Impact'] = ['sometimes', 'boolean'];
			$rules[$field . 'Block'] = ['sometimes', 'boolean'];
		}

		return $rules;
	}

	public function turnData(): TurnData
	{
		$hits = [];
		$blocks = [];

		foreach (self::TURN_ZONES as $field => $zone) {
			if ($this->boolean($field . 'Impact')) {
				$hits[] = $zone;
			}

			if ($this->boolean($field . 'Block')) {
				$blocks[] = $zone;
			}
		}

		return new TurnData(
			round: $this->integer('round'),
			opponentId: $this->integer('opponent'),
			hits: $hits,
			blocks: $blocks,
		);
	}
}
