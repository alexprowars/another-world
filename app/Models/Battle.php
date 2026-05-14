<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Battle extends Model
{
	public $table = 'battles';
	protected $guarded = [];

	/** @return HasMany<BattleMember, $this> */
	public function members(): HasMany
	{
		return $this->hasMany(BattleMember::class, 'battle_id');
	}
}
