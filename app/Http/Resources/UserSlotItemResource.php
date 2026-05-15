<?php

namespace App\Http\Resources;

use App\Models\UserItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserItem $resource
 */
class UserSlotItemResource extends JsonResource
{
	public function toArray($request): array
	{
		return [
			'id' => $this->resource->id,
			'code' => $this->resource->code,
			'type' => $this->resource->type,
			'title' => $this->resource->title,
			'position' => $this->resource->getPosition(),
			'min' => $this->resource->min,
			'max' => $this->resource->max,
			'wearout' => $this->resource->wearout,
			'wearout_max' => $this->resource->wearout_max,
			'hp' => $this->resource->hp,
			'energy' => $this->resource->energy,
			'engraving' => $this->resource->engraving,
		];
	}
}
