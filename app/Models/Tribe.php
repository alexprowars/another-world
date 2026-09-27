<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tribe extends Model
{
	protected $casts = [
		'moneys' => 'decimal:2',
	];

	/** @return HasMany<User, $this> */
	public function members(): HasMany
	{
		return $this->hasMany(User::class);
	}

	/** @return HasMany<UserItem, $this> */
	public function items(): HasMany
	{
		return $this->hasMany(UserItem::class);
	}

	/** @return HasMany<TribeLog, $this> */
	public function logs(): HasMany
	{
		return $this->hasMany(TribeLog::class);
	}
}
