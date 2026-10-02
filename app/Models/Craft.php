<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Craft extends Model
{
	protected $casts = [
		'ingredients' => 'array',
	];

	/** @return BelongsTo<Item, $this> */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}
}
