<?php

namespace App\Http\Resources;

use App\Models\Chat;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Chat
 */
class ChatMessageResource extends JsonResource
{
	public function toArray($request): array
	{
		$message = $this->message;

		$users = [];

		if (preg_match_all('/приватно \[(.*?)]/iu', $message, $match)) {
			$users = array_map('trim', $match[1]);
		}

		if (preg_match_all('/для \[(.*?)]/iu', $message, $match)) {
			$users = array_map('trim', $match[1]);

			if (!empty($users)) {
				$users = array_unique(array_merge($users, $users));
			}
		}

		$message = preg_replace('/(приватно|для) \[.*?]/iu', '', $message);

		$message = trim($message);
		$message = nl2br(str_replace(["\n", "\r"], '', $message));

		$result = [
			'id' => $this->id,
			'date' => $this->date->utc()->toAtomString(),
			'user' => $this->user->username ?? '',
			'tou' => $users,
			'toi' => $this->recipients ?? [],
			'text' => $message,
			'private' => $this->private,
			'me' => null,
			'my' => null,
		];

		$user = auth()->user();

		if ($user) {
			if (!$this->private && !empty($this->recipients)) {
				$result['me'] = in_array($user->id, $this->recipients);
				$result['my'] = $this->user_id === $user->id;
			} elseif ($this->private && !empty($this->recipients) && ($this->user_id === $user->id || in_array($user->id, $this->recipients))) {
				$result['me'] = $this->user_id !== $user->id;
				$result['my'] = !$result['me'];
			} elseif (!empty($this->recipients)) {
				$result['me'] = 0;
				$result['my'] = $this->user_id === $user->id;
			}
		}

		return $result;
	}
}
