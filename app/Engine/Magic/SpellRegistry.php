<?php

namespace App\Engine\Magic;

use App\Engine\Magic\Spells\Attack;
use App\Engine\Magic\Spells\AttackProtection;
use App\Engine\Magic\Spells\Aura;
use App\Engine\Magic\Spells\Chains;
use App\Engine\Magic\Spells\ChangeSide;
use App\Engine\Magic\Spells\CleanseAura;
use App\Engine\Magic\Spells\CleansePoison;
use App\Engine\Magic\Spells\EnergyPotion;
use App\Engine\Magic\Spells\Fireball;
use App\Engine\Magic\Spells\Heal;
use App\Engine\Magic\Spells\HealInjury;
use App\Engine\Magic\Spells\Invisibility;
use App\Engine\Magic\Spells\LightningBolt;
use App\Engine\Magic\Spells\MageArmor;
use App\Engine\Magic\Spells\MagicHand;
use App\Engine\Magic\Spells\Mirror;
use App\Engine\Magic\Spells\Reset;
use App\Engine\Magic\Spells\RestoreEnergy;
use App\Engine\Magic\Spells\Silence;
use App\Engine\Magic\Spells\Snowball;
use App\Engine\Magic\Spells\Snowstorm;
use App\Engine\Magic\Spells\StaminaPotion;
use App\Engine\Magic\Spells\StatPotion;
use App\Engine\Magic\Spells\Strip;
use App\Engine\Magic\Spells\VampireProtection;
use App\Engine\Magic\Spells\Vampirism;

class SpellRegistry
{
	public static function find(string $code): ?Spell
	{
		return match ($code) {
			'addhp50' => new Heal(50, 5),
			'addhp100' => new Heal(100, 10),
			'addhp150' => new Heal(150, 15),
			'addhp200' => new Heal(200, 20),
			'addhp250' => new Heal(250, 25),
			'addhp300' => new Heal(300, 30),
			'addenergy25' => new RestoreEnergy(25),
			'addenergy50' => new RestoreEnergy(50),
			'addenergy100' => new RestoreEnergy(100),
			'fireball30' => new Fireball(30, 10),
			'fireball40' => new Fireball(40, 15),
			'fireball50' => new Fireball(50, 20),
			'fireball65' => new Fireball(65, 25),
			'showstorm20' => new Snowstorm(20, 5),
			'showstorm30' => new Snowstorm(30, 10),
			'showstorm40' => new Snowstorm(40, 15),
			'razdet' => new Strip(),
			'invisible' => new Invisibility(),
			'reset' => new Reset(),
			'attack' => new Attack(),
			'blood_attack' => new Attack(true),
			'mirror' => new Mirror(),
			'lighting_bolt40' => new LightningBolt(40, 15),
			'lighting_bolt50' => new LightningBolt(50, 20),
			'lighting_bolt60' => new LightningBolt(60, 25),
			'lighting_bolt70' => new LightningBolt(70, 30),
			'magichand' => new MagicHand(),
			'chains' => new Chains(),
			'healing2' => new HealInjury(1, 10),
			'healing3' => new HealInjury(2, 15),
			'healing1' => new HealInjury(null, 20),
			'healing_m' => new HealInjury(null, 0, 4),
			'magearmor' => new MageArmor(),
			'mol15' => new Silence(15, 5),
			'mol30' => new Silence(30, 10),
			'mol60' => new Silence(60, 20),
			'immun' => new AttackProtection(),
			'vampire' => new Vampirism(),
			'chesnok' => new VampireProtection(3),
			'chesnok2' => new VampireProtection(12),
			'voskr' => new CleansePoison(),
			'snowball1' => new Snowball(),
			'perevod' => new ChangeSide(),
			'aura_armor1' => new Aura([
				'armor1' => 5,
				'armor2' => 5,
				'armor3' => 5,
				'armor4' => 5,
				'armor5' => 5,
			], 4),
			'aura_armor2' => new Aura([
				'armor1' => 10,
				'armor2' => 10,
				'armor3' => 10,
				'armor4' => 10,
				'armor5' => 10,
			], 4),
			'aura_armor3' => new Aura([
				'armor1' => 15,
				'armor2' => 15,
				'armor3' => 15,
				'armor4' => 15,
				'armor5' => 15,
			], 4),
			'aura_sword1' => new Aura(['min' => 5, 'max' => 5], 8),
			'aura_sword2' => new Aura(['min' => 12, 'max' => 12], 7),
			'aura_sword3' => new Aura(['min' => 18, 'max' => 18], 6),
			'aura_ochist' => new CleanseAura(),
			'addustal' => new StaminaPotion(),
			'elikenergy' => new EnergyPotion(),
			'elik_mag_4chas' => new StatPotion([
				'strength' => -2,
				'dexterity' => -2,
				'agility' => -2,
				'vitality' => -2,
				'magic' => 4,
				'intelligence' => 4,
			]),
			'elik_sila4_4chas' => new StatPotion(['strength' => 4]),
			'elagil4' => new StatPotion(['agility' => 4]),
			'eldex4' => new StatPotion(['dexterity' => 4]),
			default => null,
		};
	}
}
