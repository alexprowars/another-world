<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $table = 'items';
	protected $guarded = [];

	public function isSecondHand(): bool
	{
		return in_array($this->type, [1, 17], true) && $this->slot2 == 5;
	}

	public function getVipPrice()
	{
		if ($this->credits > 0) {
			return round($this->credits * 0.67, 2);
		} else {
			return round($this->gold * 0.85, 2);
		}
	}

	public function getMerchantPrice(bool $vip = false): float
	{
		$price = $vip ? $this->getVipPrice() : $this->gold;

		return round($price * 0.9, 2);
	}

	public function getPurchasePrice(?User $user): float
	{
		$vip = $user?->vip?->isFuture() ?? false;

		if ($this->credits > 0) {
			return $vip ? $this->getVipPrice() : $this->credits;
		}

		if ($user?->tutorial == 3 && $this->id == 817) {
			return 0;
		}

		if ($user?->profession == 8) {
			return $this->getMerchantPrice($vip);
		}

		return $vip ? $this->getVipPrice() : $this->gold;
	}
}
