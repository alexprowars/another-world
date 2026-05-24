<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $table = 'items';
	protected $guarded = [];

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
