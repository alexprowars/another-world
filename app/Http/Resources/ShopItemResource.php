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
		$item = $this->resource->item;
		$user = auth()->user();

		if ($user?->tutorial == 3 and $item->id == 817) {
			$item->price = 0;
		}

		return [
			'id' => $this->resource->id,
			'count' => $this->resource->count,
			'delivery' => $this->resource->delivery,
			'item' => [
				'id' => $item->id,
				'code' => $item->code,
				'title' => $item->title,
				'price' => $item->price,
				'price_vip' => $item->getVipPrice(),
				'credits' => $item->credits,
				'type' => $item->type,
				'mana' => $item->use_mana,
				'about' => $item->about,
				'demands' => $item->getMinDemands(),
				'bonuses' => $item->getBounus(),
			],
		];
	}
}
