<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Effect extends Model
{
	public const int AURA = 1;
	public const int POTION = 2;
	public const int INJURY = 3;
	public const int POISON = 4;

	public $timestamps = false;
	protected $table = 'effects';

	protected $casts = [
		'date' => 'datetime',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
