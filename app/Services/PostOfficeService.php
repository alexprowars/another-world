<?php

namespace App\Services;

use App\Models\MailLetter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostOfficeService
{
	public const int SEND_COST = 1;

	public static function send(User $user, string $recipientName, string $subject, string $body): MailLetter
	{
		return DB::transaction(function () use ($user, $recipientName, $subject, $body) {
			$sender = User::query()->lockForUpdate()->findOrFail($user->id);

			if ($sender->room !== 25) {
				throw ValidationException::withMessages(['letter' => 'Отправлять письма можно только на почте.']);
			}

			$recipient = User::query()->where('name', trim($recipientName))->first();

			if (!$recipient) {
				throw ValidationException::withMessages(['recipient' => 'Персонаж с таким именем не найден.']);
			}

			if ($recipient->id === $sender->id) {
				throw ValidationException::withMessages(['recipient' => 'Нельзя отправить письмо самому себе.']);
			}

			if ($sender->gold < self::SEND_COST) {
				throw ValidationException::withMessages(['letter' => 'Для отправки письма нужно ' . self::SEND_COST . ' зол.']);
			}

			$letter = MailLetter::create([
				'sender_id' => $sender->id,
				'recipient_id' => $recipient->id,
				'subject' => trim($subject),
				'body' => trim($body),
				'created_at' => now(),
			]);

			$sender->decrement('gold', self::SEND_COST);

			ChatService::sendSystemMessage(
				'Письмо отправлено персонажу ' . $recipient->name . '. Списано ' . self::SEND_COST . ' зол.',
				[$sender],
				name: 'Почта',
			);
			ChatService::sendSystemMessage(
				'Вы получили новое письмо от ' . $sender->name . '. Прочитать его можно на почте.',
				[$recipient],
				name: 'Почта',
			);

			return $letter;
		}, 3);
	}
}
