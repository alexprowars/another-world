<?php

namespace App\Engine\Battle\Abilities;

readonly class Ability
{
	public function __construct(
		public int $id,
		public string $name,
		public string $description,
		public int $level,
		public int $wait,
		public int $duration,
		public AbilityEffect $effect,
		public int $hitCost = 0,
		public int $blockCost = 0,
		public int $critCost = 0,
		public int $spiritCost = 0,
		public int $parryCost = 0,
		public int $hpCost = 0,
	) {
	}

	public function toArray(): array
	{
		return [
			'type' => 1,
			'level' => $this->level,
			'block' => $this->blockCost,
			'hit' => $this->hitCost,
			'crit' => $this->critCost,
			'magic' => $this->spiritCost,
			'parry' => $this->parryCost,
			'damage' => $this->hpCost,
			'wait' => $this->wait,
			'time' => $this->duration,
			'name' => $this->name,
			'about' => $this->description,
		];
	}
}
