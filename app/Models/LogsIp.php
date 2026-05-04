<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogsIp extends Model
{
	use MassPrunable;

	protected $table = 'logs_ips';
	protected $guarded = [];
	public $timestamps = false;

	protected $casts = [
		'created_at' => 'immutable_datetime',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return Builder<static>
	 */
	public function prunable(): Builder
	{
		return static::query()->where('created_at', '<', now()->subDays(7));
	}
}
