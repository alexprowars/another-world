<?php

namespace App\Engine\Battle\Services;

use App\Engine\Battle\Data\AttackResult;
use App\Engine\CombatStats;
use Random\Randomizer;

class AttackCalculator
{
	public function __construct(private readonly Randomizer $randomizer)
	{
	}

	/**
	 * @param array<int, int> $hits
	 * @param array<int, int> $blocks
	 */
	public function calculate(
		CombatStats $attacker,
		CombatStats $defender,
		array $hits,
		array $blocks,
	): AttackResult {
		$statsVsMod = config('battle.stats_vs_mod');

		$attackMinDamage = $attacker->min;
		$attackMaxDamage = $attacker->max;

		$armorByZone = $defender->getArmorByZone();

		// Вероятность уворота защитника
		$x = $attacker->agility + $attacker->unuv / $statsVsMod;
		$y = $defender->agility + $defender->uv / $statsVsMod;
		$dodgeChance = $this->calculateChance($x, $y);

		// Вероятность критического удара атакующего
		$x = $defender->dexterity + $defender->unkrit / $statsVsMod;
		$y = $attacker->dexterity + $attacker->krit / $statsVsMod;
		$critChance = $this->calculateChance($x, $y);

		// Расчёт вероятности пробоя блока
		$x = $defender->strength + $defender->mblock / $statsVsMod;
		$y = $attacker->strength + $attacker->pblock / $statsVsMod;
		$blockBreakChance = $this->calculateChance($x, $y);

		// Расчёт вероятности пробоя брони
		$x = $defender->strength + $defender->kbr / $statsVsMod;
		$y = $attacker->strength + $attacker->pbr / $statsVsMod;
		$armorPierceChance = $this->calculateChance($x, $y);

		$dodgeRoll = $this->randomizer->nextFloat();
		$critRoll = $this->randomizer->nextFloat();
		$armorPierceRoll = $this->randomizer->nextFloat();

		$damageByHit = [1 => 0, 2 => 0];
		$actionByHit = [1 => '', 2 => ''];

		$experienceMultiplier = 1;

		if ($dodgeChance > $dodgeRoll) {
			$actionByHit[1] = 'uvorot';
		} elseif ($critChance > $critRoll) {
			$baseMinDamage = $attacker->getMinDamage($attackMinDamage);
			$baseMaxDamage = $attacker->getMaxDamage($attackMaxDamage);

			$minDamage = (int) (1.5 * $baseMinDamage);
			$maxDamage = (int) (2.5 * $baseMaxDamage);

			$damageByHit[1] = $this->randomizer->getInt($minDamage, $maxDamage);

			if ($damageByHit[1] < 0) {
				$damageByHit[1] = 0;
			}

			$actionByHit[1] = 'crit';

			$experienceMultiplier *= 1.2;
		} else {
			for ($i = 1; $i <= count($hits); $i++) {
				if (isset($hits[$i - 1]) && $hits[$i - 1] > 0) {
					$blockBreakRoll = $this->randomizer->nextFloat();

					if (in_array($hits[$i - 1], $blocks, true)) {
						if ($blockBreakChance > $blockBreakRoll) {
							$baseMinDamage = $attacker->getMinDamage($attackMinDamage);
							$baseMaxDamage = $attacker->getMaxDamage($attackMaxDamage);

							$minDamage = (int) (0.5 * $baseMinDamage);
							$maxDamage = (int) (0.75 * $baseMaxDamage);

							$damageByHit[$i] = $this->randomizer->getInt($minDamage, $maxDamage);

							if ($damageByHit[$i] < 0) {
								$damageByHit[$i] = 0;
							}

							$actionByHit[$i] = 'prob' . $i;

							$experienceMultiplier *= 1.2;
						} else {
							$damageByHit[$i] = 0;
							$actionByHit[$i] = 'block' . $i;
						}
					} else {
						if ($armorPierceChance > $armorPierceRoll) {
							$armorByZone[$hits[$i - 1] - 1] = 0;

							$attackMinDamage = (int) ceil($attackMinDamage * 0.5);
							$attackMaxDamage = (int) ceil($attackMaxDamage * 0.5);

							$experienceMultiplier *= 1.2;
						}

						$armor = $armorByZone[$hits[$i - 1] - 1];
						$baseMinDamage = $attacker->getMinDamage($attackMinDamage);
						$baseMaxDamage = $attacker->getMaxDamage($attackMaxDamage);

						$minDamage = (int) round($baseMinDamage - $armor);
						$maxDamage = (int) round($baseMaxDamage - $armor);

						$damageByHit[$i] = $this->randomizer->getInt($minDamage, $maxDamage);

						if ($damageByHit[$i] < 0) {
							$damageByHit[$i] = 0;
						}

						$actionByHit[$i] = 'udar';
					}
				}
			}
		}

		$damage = array_sum($damageByHit) - $defender->damageReduction;

		if ($damage < 0) {
			$damage = 0;
		}

		$attackerCrits = 0;
		$attackerHits = 0;
		$defenderParry = 0;
		$defenderBlocks = 0;
		$commentId = 0;

		if ($actionByHit[1] == 'uvorot') {
			$commentId = $this->randomizer->getInt(31, 33);
			$defenderParry = 1;
		} elseif ($actionByHit[1] == 'crit') {
			$commentId = $this->randomizer->getInt(21, 23);
			$attackerCrits = 1;
		} elseif ($actionByHit[1] === 'prob1') {
			$commentId = $actionByHit[2] === 'prob2' ? 43 : 41;
			$attackerHits = 1;
		} elseif ($actionByHit[2] === 'prob2') {
			$commentId = 42;
			$attackerHits = 1;
		} elseif ($actionByHit[1] === 'block1') {
			$commentId = $actionByHit[2] === 'udar'
				? $this->randomizer->getInt(8, 10)
				: $this->randomizer->getInt(11, 20);
			$defenderBlocks = 1;
		} elseif ($actionByHit[1] === 'udar') {
			if ($actionByHit[2] === 'block2') {
				$commentId = $this->randomizer->getInt(5, 7);
				$defenderBlocks = 1;
			} else {
				$commentId = $this->randomizer->getInt(1, 4);
			}
		}

		return new AttackResult(
			damage: $damage,
			commentId: $commentId,
			attackerCrits: $attackerCrits,
			attackerHits: $attackerHits,
			defenderParry: $defenderParry,
			defenderBlocks: $defenderBlocks,
			experienceMultiplier: $experienceMultiplier,
		);
	}

	private function calculateChance(float $x, float $y): float
	{
		if ($y == 0) {
			// Равные нулевые характеристики дают тот же шанс, что и равные положительные.
			return $x == 0 ? 0.1 : 0;
		}

		$chance = 0;

		if (4 * $x <= $y) {
			$chance = 1 - 2 * $x / (5 * $y);
		} elseif (2 * $x <= $y && $y < 4 * $x) {
			$chance = 1.05 - 0.6 * $x / $y;
		} elseif (4 * $x / 3 <= $y && $y < 2 * $x) {
			$chance = 1.75 - 2 * $x / $y;
		} elseif ($x <= $y && $y < 4 * $x / 3) {
			$chance = 0.7 - 0.6 * $x / $y;
		} elseif (2 * $x / 3 <= $y && $y < $x) {
			$chance = 0.28 - 0.18 * $x / $y;
		} elseif ($x / 2 <= $y && $y < 2 * $x / 3) {
			$chance = 0.04 - 0.02 * $x / $y;
		} elseif ($y < $x / 2) {
			$chance = 0;
		}

		return $chance;
	}
}
