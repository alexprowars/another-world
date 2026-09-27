<?php

namespace App\Http\Resources;

use App\Models\Tribe;
use App\Models\User;
use App\Models\UserGift;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property UserGift $resource */
class UserGiftResource extends JsonResource
{
	public function toArray($request): array
	{
		$gift = $this->resource;
		$sender = $gift->sender;
		$senderName = 'Аноним';

		if ($gift->from === 1 && $sender instanceof User) {
			$senderName = $sender->name;
		} elseif ($gift->from === 2 && $sender instanceof Tribe) {
			$senderName = 'Клан ' . $sender->name;
		}

		return [
			'id' => $gift->id,
			'title' => $gift->item->title,
			'image' => '/assets/images/items/' . $gift->item->type . '/' . $gift->item->code . '.gif',
			'text' => $gift->text,
			'sender' => $senderName,
			'date' => $gift->date->toAtomString(),
		];
	}
}
