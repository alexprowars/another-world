<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogItem extends Model
{
	use MassPrunable;

	public $timestamps = false;
	protected $table = 'logs_items';

	protected $casts = [
		'date' => 'immutable_datetime',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return Builder<static>
	 */
	public function prunable(): Builder
	{
		return static::query()->where('date', '<', now()->subDays(90));
	}
}
