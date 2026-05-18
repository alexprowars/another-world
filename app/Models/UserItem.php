<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserItem extends Model
{
	protected $table = 'users_items';

	protected $casts = [
		'requirements' => 'json:unicode',
		'bank' => 'boolean',
		'komis' => 'boolean',
		'sclad' => 'boolean',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function getPosition()
	{
		return $this->position;
	}

	public function setPosition($i)
	{
		$this->position = $i;
	}

	public function getSellPrice(): float
	{
		if ($this->price_type == 1) {
			return round($this->price * 0.5, 2);
		} elseif ($this->type < 12) {
			return round(($this->price * (1 - ($this->wearout / ($this->wearout_max + 0.01)))) * 0.5, 2);
		} else {
			return round($this->price * 0.5, 2);
		}
	}
}
