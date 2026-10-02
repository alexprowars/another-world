<?php

namespace App\Http\Resources;

use App\Models\ShopItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ShopItem
 * @property ShopItem $resource
 */
class ShopItemResource extends JsonResource
{
	public function toArray($request): array
	{
		return [
			'id' => $this->resource->id,
			'stock' => $this->resource->stock,
			'delivery' => $this->resource->delivery,
			'item' => ItemResource::make($this->resource->item)->toArray($request),
		];
	}
}
