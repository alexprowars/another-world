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

		if ($this->req_level > 0) {
			if ($user->level < $this->req_level) {
				$result .= "<font color=red>Уровень: " . $this->req_level . "</font><br>";
			} else {
				$result .= "Уровень: " . $this->req_level . "<br>";
			}
		}

		foreach (Vars::getStats() as $code) {
			if (!isset($this->{"req_" . $code})) {
				continue;
			}

			if ($this->{"req_" . $code} > 0) {
				if ($user->{$code} < $this->{"req_" . $code}) {
					$result .= "<font color=red>" . __('main.stats.' . $code) . ": " . $this->{"req_" . $code} . "</font><br>";
				} else {
					$result .= __('main.stats.' . $code) . ": " . $this->{"req_" . $code} . "<br>";
				}
			}
		}

		// Проверка професии
		if ($this->min_proff > 0) {
			if ($user->profession != $this->req_profession) {
				$result .= "<font color=red>Профессия: " . __('main.proffessions.' . $this->req_profession) . "</font><br>";
			} else {
				$result .= "Профессия: " . __('main.proffessions.' . $this->req_profession) . "<br>";
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
		return $value > 0 ? '+' . $value : '';
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
			return round($this->gold * 0.85, 2);
		}
	}
}
