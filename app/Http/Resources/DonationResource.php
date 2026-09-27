<?php

namespace App\Http\Resources;

use App\Models\Donation;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Donation */
class DonationResource extends JsonResource
{
	public function toArray($request): array
	{
		return [
			'id' => $this->id,
			'user' => $this->user->name ?? 'Удалённый игрок',
			'amount' => $this->amount,
			'comment' => $this->comment,
		];
	}
}