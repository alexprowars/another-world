<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopItem extends Model
{
	protected $table = 'shops_items';
	protected $guarded = [];

	/** @return BelongsTo<Shop, $this> */
	public function shop(): BelongsTo
	{
		return $this->belongsTo(Shop::class, 'shop_id');
	}

	/** @return BelongsTo<Item, $this> */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, 'item_id');
	}
}
