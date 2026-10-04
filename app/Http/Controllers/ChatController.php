<?php

namespace App\Http\Controllers;

use App\Engine\Services\ChatService;
use App\Http\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
	public function last(Request $request)
	{
		$request->validate([
			'after_id' => ['sometimes', 'integer', 'min:0'],
		]);

		$query = ChatMessage::query()
			->visibleTo($request->user())
			->with(['user:id,name', 'recipients:id,name']);

		if ($request->has('after_id')) {
			$items = $query->where('id', '>', $request->integer('after_id'))
				->orderBy('id')
				->limit(100)
				->get();
		} else {
			$items = $query->orderByDesc('id')
				->limit(30)
				->get()
				->reverse()
				->values();
		}

		return ChatMessageResource::collection($items);
	}

	public function send(Request $request)
	{
		$data = $request->validate([
			'message' => ['required', 'string', 'max:180'],
		]);

		$user = $request->user();
		$body = trim($data['message']);
		$private = false;
		$recipients = [];

		$addressPattern = '/(приватно|для)\s+\[(.*?)]/iu';

		$bodyWithoutAddresses = preg_replace($addressPattern, '', $body);

		if (preg_match('/(приватно|для)\s*\[/iu', $bodyWithoutAddresses)) {
			throw ValidationException::withMessages(['message' => 'Проверьте формат адресата: приватно [имя] или для [имя].']);
		}

		$body = trim(preg_replace_callback($addressPattern, function ($match) use (&$private, &$recipients) {
			$name = trim($match[2]);

			$users = User::query()
				->select(['id', 'name'])
				->where('name', $name)
				->limit(2)
				->get();

			if ($users->count() !== 1) {
				return 'для [' . $name . ']';
			}

			$recipient = $users->firstOrFail();
			$recipients[$recipient->id] = $recipient;

			$private = $private || mb_strtolower($match[1]) === 'приватно';

			return '';
		}, $body));

		$stopwords = __('main.stopwords');

		if (is_array($stopwords)) {
			$body = strtr($body, $stopwords);
		}

		if ($body === '') {
			throw ValidationException::withMessages(['message' => 'Введите текст сообщения']);
		}

		$response = Cache::lock('chat:send:' . $user->id, 10)->get(function () use ($user, $body, $private, $recipients) {
			$user->refresh();

			if ($user->silence?->isFuture()) {
				$message = ChatService::sendSystemMessage(
					'На вас наложено заклинание молчания. Осталось молчать до: ' . $user->silence->format('d.m.Y H:i') . '!',
					[$user],
					name: 'Комментатор',
				);

				return response()->json(ChatMessageResource::make($message)->resolve(), 403);
			}

			$limitKey = 'chat:rate:' . $user->id;

			if (RateLimiter::tooManyAttempts($limitKey, 1)) {
				$warnings = RateLimiter::hit('chat:warnings:' . $user->id);

				if ($warnings >= 3) {
					DB::transaction(function () use ($user) {
						$user->update(['silence' => now()->addMinutes(10)]);
						ChatService::sendPublicSystemMessage(
							'Комментатор запретил общение персонажу ' . $user->name . ' за флуд, сроком 10 минут!',
						);
					});
				}

				$message = ChatService::sendSystemMessage(
					'Не более 1 сообщения в 5 секунд! Осталось предупреждений: ' . max(0, 3 - $warnings),
					[$user],
					name: 'Комментатор',
				);

				return response()->json(ChatMessageResource::make($message)->resolve(), 429);
			}

			RateLimiter::hit($limitKey, 5);

			if (
				$user->isAdmin()
				&& !empty($recipients)
				&& preg_match('/^\/(kick|speak)(?:\s+(15|30|60|1440))?$/u', $body, $command)
			) {
				$message = DB::transaction(function () use ($user, $recipients, $command) {
					$time = isset($command[2]) ? (int) $command[2] : 15;

					User::query()->whereKey(array_keys($recipients))->update([
						'silence' => $command[1] === 'speak' ? null : now()->addMinutes($time),
					]);

					$names = implode(', ', array_map(fn (User $recipient) => $recipient->name, $recipients));

					$body = $command[1] === 'speak'
						? 'Модератор ' . $user->name . ' разрешил общение персонажам ' . $names . '.'
						: 'Модератор ' . $user->name . ' запретил общение персонажам ' . $names . ' на ' . $time . ' минут.';

					return ChatService::sendPublicSystemMessage($body);
				});
			} else {
				$message = $private
					? ChatService::sendPrivateMessage($user, $body, $recipients)
					: ChatService::sendPublicMessage($user, $body, $recipients);
			}

			return ChatMessageResource::make($message);
		});

		if ($response === false) {
			return response()->json([
				'message' => 'Сообщение уже отправляется. Попробуйте ещё раз.',
			], 429);
		}

		return $response;
	}

	public function online()
	{
		$users = User::query()
			->with(['tribe'])
			->where(function ($query) {
				$query->whereNull('rank')
					->orWhereNot('rank', 60);
			})
			->where('online', '>=', now()->subMinutes(5))
			->get();

		$userList = [];

		foreach ($users as $user) {
			$pl = [
				'id' => $user->id,
				'name' => $user->name,
				'rank' => $user->rank,
				'tribe' => $user->tribe?->only(['id', 'name']),
				'level' => $user->level,
				'battle' => $user->battle_id,
				'profession' => $user->profession,
				'status' => $user->options['presence_status'] ?? 0,
				'travma' => $user->injury?->isFuture() ? $user->injury->utc()->toAtomString() : null,
				'silence' => $user->silence?->isFuture() ? $user->silence->utc()->toAtomString() : null,
			];

			if ($user->invisible?->isFuture()) {
				$pl['name'] = 'Тень';
				$pl['rank'] = 0;
				$pl['tribe'] = null;
				$pl['level'] = '??';
				$pl['id'] = 1699638901;
				$pl['travma'] = null;
				$pl['battle'] = null;
				$pl['silence'] = null;
				$pl['profession'] = 0;
				$pl['status'] = 0;
			}

			$userList[] = $pl;
		}

		return response()->json([
			'users'	=> $userList,
		]);
	}
}
