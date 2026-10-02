<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Item
 * @property Item $resource
 */
class ItemResource extends JsonResource
{
	public function toArray($request): array
	{
		$item = $this->resource;
		$user = $request->user();
		$price = $user?->tutorial == 3 && $item->id == 817 ? 0 : $item->gold;

		return [
			'id' => $item->id,
			'code' => $item->code,
			'title' => $item->title,
			'price' => $price,
			'price_buy' => $item->getPurchasePrice($user),
			'credits' => $item->credits,
			'type' => $item->type,
			'wearout' => $item->wearout,
			'mana' => $item->use_mana,
			'about' => $item->about,
			'life' => $item->life,
			'requirements' => array_filter([
				'level' => $item->req_level,
				'profession' => $item->req_profession,
				'strength' => $item->req_strength,
				'dexterity' => $item->req_dexterity,
				'agility' => $item->req_agility,
				'vitality' => $item->req_vitality,
				'intelligence' => $item->req_intelligence,
			]),
			'bonuses' => array_filter([
				'min' => $item->min,
				'max' => $item->max,
				'strength' => $item->strength,
				'dexterity' => $item->dexterity,
				'agility' => $item->agility,
				'vitality' => $item->vitality,
				'intelligence' => $item->intelligence,
				'armor1' => $item->armor1,
				'armor2' => $item->armor2,
				'armor3' => $item->armor3,
				'armor4' => $item->armor4,
				'armor5' => $item->armor5,
				'krit' => $item->krit,
				'mkrit' => $item->mkrit,
				'unkrit' => $item->unkrit,
				'uv' => $item->uv,
				'unuv' => $item->unuv,
				'pblock' => $item->pblock,
				'mblock' => $item->mblock,
				'pbr' => $item->pbr,
				'kbr' => $item->kbr,
				'metk' => $item->metk,
				'hp' => $item->hp,
				'energy' => $item->energy,
				'poison' => $item->poison,
			]),
		];
	}
}
