<?php

namespace App\Models;

use App\Engine\Battle\BattleStatus;
use App\Engine\Battle\BattleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Battle extends Model
{
	public $table = 'battles';
	protected $guarded = [];

	protected $casts = [
		'started_at' => 'immutable_datetime',
		'round_at' => 'immutable_datetime',
		'use_weapons' => 'boolean',
		'is_blood' => 'boolean',
		'type' => BattleType::class,
		'status' => BattleStatus::class,
	];

	/** @return HasMany<BattleMember, $this> */
	public function members(): HasMany
	{
		return $this->hasMany(BattleMember::class, 'battle_id');
	}

	/** @return HasMany<BattleLog, $this> */
	public function logs(): HasMany
	{
		return $this->hasMany(BattleLog::class, 'battle_id');
	}
}
