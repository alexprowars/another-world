<?php

namespace App\Services;

use App\Events\ChatPublicMessage;
use App\Events\ChatPrivateMessage;
use App\Http\Resources\ChatMessageResource;
use App\Models\Chat;
use App\Models\User;

class ChatService
{
	public static function insertInChat(?User $user, string $message, bool $isPrivate = true, ?string $redirect = null)
	{
		$result = Chat::create([
			'user_id' => $user?->id,
			'message' => $message,
			'date' => now(),
			'private' => $isPrivate,
		]);

		$parsed = ChatMessageResource::make($result)->resolve();
		$parsed['redirect'] = $redirect;

		event(new ChatPrivateMessage(auth()->id(), $parsed));
	}

	public static function sendSystemMessage(User $user, string $from, string $message, bool $isPrivate = true, ?string $redirect = null)
	{
		$data = [
			'id' => null,
			'date' => now()->utc()->toAtomString(),
			'user' => $from,
			'tou' => [$user->nickname],
			'toi' => [$user->id],
			'text' => $message,
			'private' => $isPrivate,
			'me' => true,
			'my' => null,
		];

		if ($isPrivate) {
			event(new ChatPrivateMessage(auth()->id(), $data));
		} else {
			event(new ChatPublicMessage($data));
		}
	}
}
