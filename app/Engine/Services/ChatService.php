<?php

namespace App\Engine\Services;

use App\Events\ChatPrivateMessage;
use App\Events\ChatPublicMessage;
use App\Exceptions\Exception;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatService
{
	/** @param array<User> $recipients */
	public static function sendPublicMessage(User $author, string $body, array $recipients = []): ChatMessage
	{
		return self::createMessage($author, $body, $recipients, ChatMessage::VISIBILITY_PUBLIC);
	}

	/** @param array<User> $recipients */
	public static function sendPrivateMessage(User $author, string $body, array $recipients): ChatMessage
	{
		return self::createMessage($author, $body, $recipients, ChatMessage::VISIBILITY_PRIVATE);
	}

	/** @param array<User> $recipients */
	public static function sendSystemMessage(string $body, array $recipients, ?string $redirect = null, string $name = 'Система'): ChatMessage
	{
		return self::createMessage(null, $body, $recipients, ChatMessage::VISIBILITY_PRIVATE, $redirect, $name);
	}

	public static function sendPublicSystemMessage(string $body, string $name = 'Система'): ChatMessage
	{
		return self::createMessage(null, $body, [], ChatMessage::VISIBILITY_PUBLIC, null, $name);
	}

	/** @param array<User> $recipients */
	private static function createMessage(
		?User $author,
		string $body,
		array $recipients,
		string $visibility,
		?string $redirect = null,
		?string $systemName = null,
	): ChatMessage
	{
		$body = trim($body);

		if ($body === '') {
			throw new Exception('Введите текст сообщения');
		}

		if ($visibility === ChatMessage::VISIBILITY_PRIVATE && empty($recipients)) {
			throw new Exception('Укажите получателя приватного сообщения');
		}

		return DB::transaction(function () use ($author, $body, $recipients, $visibility, $redirect, $systemName) {
			$recipientIds = array_values(array_unique(
				array_map(fn (User $user) => $user->id, $recipients),
			));

			if (User::query()->whereKey($recipientIds)->count() !== count($recipientIds)) {
				throw new Exception('Один из получателей сообщения не существует');
			}

			$message = ChatMessage::create([
				'user_id' => $author?->id,
				'kind' => $author ? ChatMessage::KIND_PLAYER : ChatMessage::KIND_SYSTEM,
				'visibility' => $visibility,
				'system_name' => $systemName,
				'body' => $body,
				'redirect' => $redirect,
				'created_at' => now(),
			]);

			$message->recipients()->attach($recipientIds);
			$message->load(['user:id,name', 'recipients:id,name']);

			DB::afterCommit(fn () => self::broadcast($message));

			return $message;
		});
	}

	private static function broadcast(ChatMessage $message): void
	{
		$payload = ChatMessageResource::make($message)->resolve();

		if ($message->visibility === ChatMessage::VISIBILITY_PUBLIC) {
			event(new ChatPublicMessage($payload));

			return;
		}

		$userIds = $message->recipients->modelKeys();

		if ($message->user_id !== null) {
			$userIds[] = $message->user_id;
		}

		foreach (array_unique($userIds) as $userId) {
			event(new ChatPrivateMessage($userId, $payload));
		}
	}
}
