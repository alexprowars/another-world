<?php

namespace App\Engine;

class CombatStats
{
	public int $armor1 = 0;
	public int $armor2 = 0;
	public int $armor3 = 0;
	public int $armor4 = 0;
	public int $armor5 = 0;

	public int $min = 0;
	public int $max = 0;
	public int $damageReduction = 0;
	public bool $forceCrit = false;
	public bool $forceDodge = false;

	public int $krit = 0;
	public int $unkrit = 0;
	public int $uv = 0;
	public int $unuv = 0;

	public int $mblock = 0;
	public int $pblock = 0;
	public int $pbr = 0;
	public int $kbr = 0;
	public int $mkrit = 0;

	public function __construct(
		public int $strength = 0,
		public int $dexterity = 0,
		public int $agility = 0,
		public int $vitality = 0,
		public int $magic = 0,
		public int $intelligence = 0,
	) {
	}

	public function getMinDamage(?int $min = null): float
	{
		return $this->strength / 3 + ($min ?? $this->min);
	}

	public function getMaxDamage(?int $max = null): float
	{
		return $this->strength / 1.5 + ($max ?? $this->max);
	}

	/** @return array{int, int, int, int, int} */
	public function getArmorByZone(): array
	{
		return [
			$this->armor1,
			$this->armor2,
			$this->armor3,
			$this->armor4,
			$this->armor5,
		];
	}

	public function getMinMagicDamage(): float
	{
		return $this->intelligence / 1.5;
	}

	public function getMaxMagicDamage(): int
	{
		return 1 + $this->intelligence;
	}

	public function addStatModifiers(): void
	{
		$this->krit += $this->dexterity * 5;
		$this->unkrit += $this->dexterity * 5;
		$this->uv += $this->agility * 5;
		$this->unuv += $this->agility * 5;
	}
}
