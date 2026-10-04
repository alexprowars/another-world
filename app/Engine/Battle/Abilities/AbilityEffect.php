<?php

namespace App\Engine\Battle\Abilities;

readonly class AbilityEffect
{
	public function __construct(
		public int $damageBonus = 0,
		public bool $forceCrit = false,
		public bool $forceDodge = false,
		public int $healing = 0,
		public int $damageReduction = 0,
	) {
	}
}
