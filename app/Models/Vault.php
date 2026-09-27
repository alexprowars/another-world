<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vault extends Model
{
	public $timestamps = false;

	protected $casts = [
		'heal_at' => 'immutable_datetime',
	];
}
