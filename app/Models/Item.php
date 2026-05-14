<?php

namespace App\Models;

use App\Facades\Vars;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $table = 'items';
	protected $guarded = [];

	public function getMinDemands(): string
	{
		$result = '';

		$user = auth()->user();

		if ($this->min_level > 0) {
			if ($user->level < $this->min_level) {
				$result .= "<font color=red>Уровень: " . $this->min_level . "</font><br>";
			} else {
				$result .= "Уровень: " . $this->min_level . "<br>";
			}
		}

		foreach (Vars::getStats() as $code) {
			if (!isset($this->{"min_" . $code})) {
				continue;
			}

			if ($this->{"min_" . $code} > 0) {
				if ($user->{$code} < $this->{"min_" . $code}) {
					$result .= "<font color=red>" . __('main.stats.' . $code) . ": " . $this->{"min_" . $code} . "</font><br>";
				} else {
					$result .= __('main.stats.' . $code) . ": " . $this->{"min_" . $code} . "<br>";
				}
			}
		}

		// Проверка професии
		if ($this->min_proff > 0) {
			if ($user->profession != $this->min_proff) {
				$result .= "<font color=red>Профессия: " . __('main.proffessions.' . $this->min_proff) . "</font><br>";
			} else {
				$result .= "Профессия: " . __('main.proffessions.' . $this->min_proff) . "<br>";
			}
		}

		return $result;
	}

	public function getBounus(): string
	{
		$result = '';

		if ($this->min > 0) {
			$result .= "Минимальный урон: " . $this->formatStat($this->min) . "<br>";
		}

		if ($this->max > 0) {
			$result .= "Максимальный урон: " . $this->formatStat($this->max) . "<br>";
		}

		if ($this->br1 > 0) {
			$result .= "Броня головы: " . $this->formatStat($this->br1) . "<br>";
		}

		if ($this->br2 > 0) {
			$result .= "Броня груди: " . $this->formatStat($this->br2) . "<br>";
		}

		if ($this->br3 > 0) {
			$result .= "Броня живота: " . $this->formatStat($this->br3) . "<br>";
		}

		if ($this->br4 > 0) {
			$result .= "Броня пояса: " . $this->formatStat($this->br4) . "<br>";
		}

		if ($this->br5 > 0) {
			$result .= "Броня ног: " . $this->formatStat($this->br5) . "<br>";
		}

		foreach (Vars::getStats() as $code) {
			if (!isset($this->{$code})) {
				continue;
			}

			if ($this->{$code} != 0) {
				$result .= __('main.stats.' . $code) . ": " . $this->formatStat($this->{$code}) . "<br>";
			}
		}

		if ($this->krit > 0) {
			$result .= "Критического удара: " . $this->formatStat($this->krit) . "%<br>";
		}

		if ($this->mkrit > 0) {
			$result .= "Мощность крит. удара: " . $this->formatStat($this->mkrit) . "%<br>";
		}

		if ($this->unkrit > 0) {
			$result .= "Против критического удара: " . $this->formatStat($this->unkrit) . "%<br>";
		}

		if ($this->uv > 0) {
			$result .= "Увёртливости: " . $this->formatStat($this->uv) . "%<br>";
		}

		if ($this->unuv > 0) {
			$result .= "Против увёртливости: " . $this->formatStat($this->unuv) . "%<br>";
		}

		if ($this->pblock > 0) {
			$result .= "Пробивание блока: " . $this->formatStat($this->pblock) . "%<br>";
		}

		if ($this->hp > 0) {
			$result .= "Уровень жизни: " . $this->formatStat($this->hp) . "<br>";
		}

		if ($this->energy > 0) {
			$result .= "Уровень маны: " . $this->formatStat($this->energy) . "<br>";
		}

		return $result;
	}

	public function formatStat($value)
	{
		return ($value > 0 ? '+' . $value : '');
	}

	public function isSecondHand()
	{
		return ($this->type == 17 && $this->slot2 == 5);
	}

	public function getVipPrice()
	{
		if ($this->credits > 0) {
			return round($this->credits * 0.67, 2);
		} else {
			return round($this->price * 0.85, 2);
		}
	}

	public function addInInventory($userId)
	{
		$inf = $this->name . "|" . $this->title . "|" . $this->price . "|0|" . (int) $this->isSecondHand() . "|" . $this->art . "|0|" . $this->iznos;
		$min = $this->min_level . "|" . $this->min_strength . "|" . $this->min_dex . "|" . $this->min_agility . "|" . $this->min_vitality . "|" . $this->min_razum . "|0|" . $this->min_proff;

		return UserItem::query()->create([
			'user_id'	=> $userId,
			'inf'		=> $inf,
			'min'		=> $min,
			'type'		=> $this->type,
			'br1'		=> $this->br1,
			'br2'		=> $this->br2,
			'br3'		=> $this->br3,
			'br4'		=> $this->br4,
			'br5'		=> $this->br5,
			'min_d'		=> $this->min,
			'max_d'		=> $this->max,
			'hp'		=> $this->hp,
			'energy'	=> $this->energy,
			'strength'	=> $this->strength,
			'dex'		=> $this->dex,
			'agility'	=> $this->agility,
			'vitality'	=> $this->vitality,
			'razum'		=> $this->razum,
			'krit'		=> $this->krit,
			'mkrit'		=> $this->mkrit,
			'unkrit'	=> $this->unkrit,
			'uv'		=> $this->uv,
			'unuv'		=> $this->unuv,
			'pblock'	=> $this->pblock,
			'mblock'	=> $this->mblock,
			'pbr'		=> $this->pbr,
			'time'		=> time(),
			'about'		=> $this->about,
			'class'		=> $this->class,
			'otravl'	=> $this->otravl,
			'use_mana'	=> $this->use_mana,
			'magic'		=> $this->magic,
			'life'		=> $this->life > 0 ? (time() + $this->life) : 0
		]);
	}
}
