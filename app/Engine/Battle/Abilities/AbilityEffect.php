<?php

namespace App\Engine\Battle\Abilities;

readonly class AbilityEffect
{
	public function __construct(
		public int $damageBonus = 0,
		public int $critBonus = 0,
		public int $dodgeBonus = 0,
		public int $healing = 0,
		public int $damageReduction = 0,
	) {
	}
}
