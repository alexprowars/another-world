<?php

namespace App\Http\Resources;

use App\Models\MarketItem;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MarketItem */
class MarketItemResource extends JsonResource
{
	public function toArray($request): array
	{
		return [
			'id' => $this->id,
			'price' => $this->price,
			'is_own' => $this->user_id === $request->user()->id,
			'seller' => $this->user->name,
			'item' => InventoryItemResource::make($this->item),
		];
	}
}