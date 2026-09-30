<?php

namespace App\Engine\Battle\Data;

readonly class AttackResult
{
	public function __construct(
		public int $damage,
		public int $commentId,
		public int $attackerCrits,
		public int $attackerHits,
		public int $defenderParry,
		public int $defenderBlocks,
		public float $experienceMultiplier,
	) {
	}
}
