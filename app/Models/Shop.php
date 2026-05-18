<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
	protected $table = 'shops';
	protected $guarded = [];
	public $timestamps = false;

	/** @return HasMany<ShopItem, $this> */
	public function items(): HasMany
	{
		return $this->hasMany(ShopItem::class, 'shop_id');
	}
}
