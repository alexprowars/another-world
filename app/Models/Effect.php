<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Effect extends Model
{
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
