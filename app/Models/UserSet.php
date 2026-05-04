<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSet extends Model
{
	protected $table = 'users_sets';
	protected $guarded = [];

	protected $casts = [
		'items' => 'json:unicode',
	];

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function wear(): void
	{
		$user = auth()->user();

		$items = $this->items;

		for ($i = 1; $i <= 22; $i++) {
			if (!empty($items['i' . $i])) {
				$object = UserItem::query()
					->whereBelongsTo($user)
					->whereKey($items['i' . $i])
					->whereNull('bank')
					->whereNull('komis')
					->whereNull('sclad')
					->first();

				if (!$object) {
					unset($items['i' . $i]);
				} else {
					$obj_inf = explode("|", $object->inf);
					$obj_min = explode("|", $object->min);

					if (($user->level < $obj_min[0] || $user->strength < $obj_min[1] || $user->dex < $obj_min[2] || $user->agility < $obj_min[3] || $user->vitality < $obj_min[4] || $user->razum < $obj_min[5] || ($obj_min[7] != 0 && $user->proff != $obj_min[7])) || $object->tip == 13 || $obj_inf[6] >= $obj_inf[7]) {
						unset($items['i' . $i]);
					} else {
						$object->onset = $i;
						$object->save();
					}
				}
			} else {
				unset($items['i' . $i]);
			}
		}

		$this->items = $items;
		$this->save();
	}
}
