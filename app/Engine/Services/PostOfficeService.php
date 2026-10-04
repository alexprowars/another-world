<?php

namespace App\Engine\Services;

use App\Models\MailLetter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostOfficeService
{
	public static function send(User $user, string $recipientName, string $subject, string $body): MailLetter
	{
		return DB::transaction(function () use ($user, $recipientName, $subject, $body) {
			$sender = User::query()->lockForUpdate()->findOrFail($user->id);
			$sendCost = config('game.postoffice.send_cost');

			if (!$sender->currentLocation()->is('post-office')) {
				throw ValidationException::withMessages(['letter' => 'Отправлять письма можно только на почте.']);
			}

			$recipient = User::query()->where('name', trim($recipientName))->first();

			if (!$recipient) {
				throw ValidationException::withMessages(['recipient' => 'Персонаж с таким именем не найден.']);
			}

			if ($recipient->id === $sender->id) {
				throw ValidationException::withMessages(['recipient' => 'Нельзя отправить письмо самому себе.']);
			}

			if ($sender->gold < $sendCost) {
				throw ValidationException::withMessages(['letter' => 'Для отправки письма нужно ' . $sendCost . ' зол.']);
			}

			$letter = MailLetter::create([
				'sender_id' => $sender->id,
				'recipient_id' => $recipient->id,
				'subject' => trim($subject),
				'body' => trim($body),
				'created_at' => now(),
			]);

			$sender->decrement('gold', $sendCost);

			ChatService::sendSystemMessage(
				'Письмо отправлено персонажу ' . $recipient->name . '. Списано ' . $sendCost . ' зол.',
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
