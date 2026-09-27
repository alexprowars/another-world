<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Work extends Model
{
	public $timestamps = false;

	/** @return BelongsTo<WorkType, $this> */
	public function type(): BelongsTo
	{
		return $this->belongsTo(WorkType::class, 'work_type_id');
	}
}
