<?php

namespace App\Engine\Battle\Data;

readonly class TurnData
{
	/**
	 * @param list<int> $hits
	 * @param list<int> $blocks
	 */
	public function __construct(
		public int $round,
		public int $opponentId,
		public array $hits,
		public array $blocks,
	) {
	}

	public function hasActions(): bool
	{
		return !empty($this->hits) || !empty($this->blocks);
	}
}
