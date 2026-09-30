<?php

namespace App\Http\Resources;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ChatMessage */
class ChatMessageResource extends JsonResource
{
	public function toArray($request): array
	{
		return [
			'id' => $this->id,
			'sender' => $this->user?->only(['id', 'name']),
			'recipients' => $this->recipients
				->map(fn (User $user) => $user->only(['id', 'name']))
				->values()
				->all(),
			'kind' => $this->kind,
			'visibility' => $this->visibility,
			'system_name' => $this->system_name,
			'body' => $this->body,
			'created_at' => $this->created_at->utc()->toAtomString(),
			'redirect' => $this->redirect,
		];
	}
}
