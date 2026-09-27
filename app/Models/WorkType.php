<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkType extends Model
{
	public $timestamps = false;

	/** @return HasMany<Work, $this> */
	public function works(): HasMany
	{
		return $this->hasMany(Work::class, 'work_type_id');
	}
}
