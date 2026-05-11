<?php

namespace App;

use App\Facades\Vars;
use App\Models\Effect;
use App\Models\User;

class CharacterCalculator
{
	public $strength = 0;
	public $dex = 0;
	public $agility = 0;
	public $vitality = 0;
	public $power = 0;
	public $razum = 0;
	public $battery = 0;

	public $br1 = 0;
	public $br2 = 0;
	public $br3 = 0;
	public $br4 = 0;
	public $br5 = 0;

	public $min = 0;
	public $max = 0;

	public $krit	= 0;
	public $unkrit	= 0;
	public $uv		= 0;
	public $unuv	= 0;

	public $mblock	= 0;
	public $pbr		= 0;
	public $kbr		= 0;
	public $pblock	= 0;
	public $mkrit	= 0;

	public function __construct(protected User $user)
	{
		$this->strength = $this->user->strength;
		$this->dex = $this->user->dex;
		$this->agility = $this->user->agility;
		$this->vitality = $this->user->vitality;
		$this->power = $this->user->power;
		$this->razum = $this->user->razum;
		$this->battery = $this->user->battery;
	}

	public function checkEffects()
	{
		if ($this->user->provin == 1) {
			$this->battery = 1;
		}

		//$user['hp'] = 0;
		//$user['energy'] = 0;

		// МФ
		$this->krit		+= ($this->dex * 5);
		$this->unkrit	+= ($this->dex * 5);
		$this->uv		+= ($this->agility * 5);
		$this->unuv		+= ($this->agility * 5);

		// Положительные и отрицательные эффекты на персонаже (элики, ауры, проклятья)
		$effects = $this->user->effects()
			->whereFuture('date')
			->get();

		/** @var Effect $effect */
		foreach ($effects as $effect) {
			//$this->auraInfo[] = $effect;

			foreach (Vars::getStats() as $stat) {
				if (isset($effect[$stat])) {
					$this->{$stat} += $effect[$stat];
				}
			}

			$this->br1 += $effect->br1 ?? 0;
			$this->br2 += $effect->br2 ?? 0;
			$this->br3 += $effect->br3 ?? 0;
			$this->br4 += $effect->br4 ?? 0;
			$this->br5 += $effect->br5 ?? 0;
			$this->min += $effect->min ?? 0;
			$this->max += $effect->max ?? 0;

			//$this->effects++;
		}

		// Конец эффектов

		foreach (Vars::getStats() as $stat) {
			if ($this->{$stat} < 0) {
				$this->{$stat} = 0;
			}
		}

		// HP, Energy, Battery

		$hp_max = $this->vitality * 5 + $this->hp;
		$this->energy_max = ceil($this->power * 5 + $this->energy);
		$this->ustal_max = $this->battery * 20;

		if ($this->hp_max != $hp_max) {
			$this->hp_max = $hp_max;
		}

		if ($this->hp_now > $this->hp_max) {
			$this->hp_now = $this->hp_max;
		}

		if ($this->energy_now > $this->energy_max) {
			$this->energy_now = $this->energy_max;
		}

		if ($this->ustal_now > $this->ustal_max) {
			$this->ustal_now = $this->ustal_max;
		}

		$this->update();
	}
}
