<?php

namespace App\Http\Resources;

use App\Models\MailLetter;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MailLetter */
class MailLetterResource extends JsonResource
{
	public function toArray($request): array
	{
		return [
			'id' => $this->id,
			'sender' => $this->sender->only(['id', 'name']),
			'recipient' => $this->recipient->only(['id', 'name']),
			'subject' => $this->subject,
			'read_at' => $this->read_at?->toAtomString(),
			'created_at' => $this->created_at->toAtomString(),
		];
	}
}
