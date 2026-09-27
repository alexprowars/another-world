<?php

namespace App\Services;

use App\Events\ChatPrivateMessage;
use App\Exceptions\Exception;
use App\Http\Resources\ChatMessageResource;
use App\Models\Chat;
use App\Models\LogTransfer;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Support\Facades\DB;

class TransferService
{
	public static function canTransfer(User $user): bool
	{
		return $user->level >= 6 || $user->isAdmin();
	}

	public static function itemRestriction(User $user, UserItem $item): ?string
	{
		if ($item->user_id != $user->id || $item->onset || $item->bank || $item->market || $item->pawnshop
			|| in_array($item->id, $user->getSlot()->getItemsId())) {
			return 'Предмет недоступен для передачи!';
		}

		if ($item->present) {
			return 'Вы не можете передавать подарки!';
		}

		if ($item->artifact && !$user->isAdmin() && $user->rank != 30) {
			return 'Вы не можете передавать артефакты!';
		}

		return null;
	}

	public static function findRecipient(User $user, string $login): User
	{
		$recipient = User::query()->where(ctype_digit($login) ? 'id' : 'name', $login)->first();

		if (!$recipient) {
			throw new Exception('Персонаж не существует!');
		}

		if ($recipient->is($user)) {
			throw new Exception('Вы не можете передать что-либо самому себе!');
		}

		return $recipient;
	}

	public static function transferItem(User $user, int $recipientId, int $itemId, ?string $ip): LogTransfer
	{
		return DB::transaction(function () use ($user, $recipientId, $itemId, $ip) {
			$item = UserItem::query()->whereBelongsTo($user)->lockForUpdate()->find($itemId);

			if (!$item) {
				throw new Exception('Предмет не найден в Вашем рюкзаке!');
			}

			[$sender, $recipient] = self::lockParticipants($user, $recipientId);

			if ($restriction = self::itemRestriction($sender, $item)) {
				throw new Exception($restriction);
			}

			if ($recipient->rank == 70 && (!$recipient->online || $recipient->online->lessThan(now()->subSeconds(400)))) {
				throw new Exception('Персонаж находится не в игре!');
			}

			$item->update(['user_id' => $recipient->id]);

			$transfer = LogTransfer::create([
				'sender_id' => $sender->id,
				'recipient_id' => $recipient->id,
				'user_item_id' => $item->id,
				'item_title' => $item->title,
				'ip' => $ip,
			]);

			self::notify($recipient, '<b>' . e($sender->name) . '</b> передал Вам предмет <b>' . e($item->title) . '</b>.');

			return $transfer;
		}, 3);
	}

	public static function transferGold(User $user, int $recipientId, float $amount, string $comment, ?string $ip): LogTransfer
	{
		if (!is_finite($amount) || $amount <= 0 || $amount > 9999999999.99 || round($amount, 2) != $amount) {
			throw new Exception('Укажите положительную сумму с точностью до сотых.');
		}

		$comment = trim($comment);

		if ($comment === '' || mb_strlen($comment) > 255) {
			throw new Exception('Укажите причину передачи длиной до 255 символов.');
		}

		return DB::transaction(function () use ($user, $recipientId, $amount, $comment, $ip) {
			[$sender, $recipient] = self::lockParticipants($user, $recipientId);

			if ($sender->gold < $amount) {
				throw new Exception('У Вас недостаточно золота для передачи!');
			}

			$sender->update(['gold' => round($sender->gold - $amount, 2)]);
			$recipient->update(['gold' => round($recipient->gold + $amount, 2)]);

			$transfer = LogTransfer::create([
				'sender_id' => $sender->id,
				'recipient_id' => $recipient->id,
				'gold' => $amount,
				'comment' => $comment,
				'ip' => $ip,
			]);

			self::notify($recipient, 'Персонаж <b>' . e($sender->name) . '</b> передал Вам <b>' . $amount . '</b> зол.');

			return $transfer;
		}, 3);
	}

	/** @return array{User, User} */
	private static function lockParticipants(User $user, int $recipientId): array
	{
		if ($recipientId === $user->id) {
			throw new Exception('Вы не можете передать что-либо самому себе!');
		}

		$users = User::query()->whereKey([$user->id, $recipientId])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
		$sender = $users->get($user->id);
		$recipient = $users->get($recipientId);

		if (!$sender || !$recipient) {
			throw new Exception('Персонаж не существует!');
		}

		if (!self::canTransfer($sender)) {
			throw new Exception('Передачи разрешены только персонажам начиная с 6 уровня!');
		}

		return [$sender, $recipient];
	}

	private static function notify(User $recipient, string $text): void
	{
		$message = Chat::create([
			'message' => $text,
			'recipients' => [$recipient->id],
			'private' => true,
			'date' => now(),
		]);

		DB::afterCommit(function () use ($recipient, $message) {
			event(new ChatPrivateMessage($recipient->id, ChatMessageResource::make($message)->resolve()));
		});
	}
}