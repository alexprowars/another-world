<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailLetter extends Model
{
	public $timestamps = false;

	protected $casts = [
		'read_at' => 'immutable_datetime',
		'created_at' => 'immutable_datetime',
	];

	/** @return BelongsTo<User, $this> */
	public function sender(): BelongsTo
	{
		return $this->belongsTo(User::class)->withTrashed();
	}

	/** @return BelongsTo<User, $this> */
	public function recipient(): BelongsTo
	{
		return $this->belongsTo(User::class)->withTrashed();
	}
}
