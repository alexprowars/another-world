<?php

namespace App\Http\Resources;

use App\Models\UserItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserItem
 * @property UserItem $resource
 */
class InventoryItemResource extends JsonResource
{
	public function toArray($request): array
	{
		return [
			'id' => $this->id,
			'code' => $this->code,
			'title' => $this->title,
			'type' => $this->type,
			'requirements' => $this->requirements,
			'wearout' => $this->wearout,
			'wearout_max' => $this->wearout_max,
			'price' => $this->price,
			'about' => $this->about,
			'strength' => $this->strength,
			'dexterity' => $this->dexterity,
			'agility' => $this->agility,
			'vitality' => $this->vitality,
			'intelligence' => $this->intelligence,
			'armor1' => $this->armor1,
			'armor2' => $this->armor2,
			'armor3' => $this->armor3,
			'armor4' => $this->armor4,
			'armor5' => $this->armor5,
			'min' => $this->min,
			'max' => $this->max,
			'krit' => $this->krit,
			'unkrit' => $this->unkrit,
			'uv' => $this->uv,
			'unuv' => $this->unuv,
			'mkrit' => $this->mkrit,
			'pblock' => $this->pblock,
			'mblock' => $this->mblock,
			'pbr' => $this->pbr,
			'kbr' => $this->kbr,
			'metk' => 0,
			'hp' => $this->hp,
			'energy' => $this->energy,
			'poison' => $this->poison,
			'magic' => $this->magic,
		];
	}
}
