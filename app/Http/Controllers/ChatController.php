<?php

namespace App\Http\Controllers;

use App\Events\ChatPublicMessage;
use App\Events\ChatPrivateMessage;
use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Models\Chat;
use App\Models\User;
use App\Services\ChatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
	public function last()
	{
		$items = Chat::query()
			->with(['user'])
			->orderByDesc('id')
			->limit(30);

		$lastMessage = Chat::query()
			->orderByDesc('id')
			->value('id') ?? 0;

		if ($lastMessage) {
			$items->where(function ($query) use ($lastMessage) {
				$query->where('id', '>=', $lastMessage - 30)
					->orWhere('date', '>', Carbon::now()->subMinutes(30));
			});
		}

		$items = $items->get();

		return ChatMessageResource::collection($items->reverse());
	}

	public function send(Request $request)
	{
		$message = trim(addslashes(Str::sanitize($request->post('message'))));

		if (empty($message)) {
			throw new Exception('Введите текст сообщения');
		}

		$user = $request->user();

		if ($user->silence?->isFuture()) {
			ChatService::sendSystemMessage(
				$user,
				'Коментатор',
				'На вас наложено заклинание молчания. Осталось молчать до: ' . $user->silence->format('d.m.Y H:i') . '!'
			);

			return;
		}

		if (session()->get('chat_spam', 0) == 0) {
			session()->put('chat_spam', time() - 5);
		}

		if (session()->get('chat_alert') === null) {
			session()->put('chat_alert', 0);
		}

		if (session()->get('chat_spam', 0) >= time()) {
			ChatService::sendSystemMessage(
				$user,
				'Коментатор',
				'Не более 1 сообщения в 5 секунд! Осталось предупреждений: ' . (2 - session()->get('chat_alert', 0))
			);

			if (session()->get('chat_alert', 0) === 0) {
				session()->put('chat_alert_time', time());
			}

			session()->put('chat_alert', session()->get('chat_alert', 0) + 1);

			if (session()->get('chat_alert', 0) > 2 && session()->get('chat_alert_time', 0) > time() - 60) {
				$user->silence = now()->addMinutes(10);
				$user->save();

				ChatService::insertInChat(
					null,
					'<u><b>Комментатор</b></u> запретил общение  персонажу <u><b>' . $user->nickname . '</b></u> за флуд, сроком 10 минут!',
					false
				);
			}

			return;
		} else {
			session()->put('chat_spam', time() + 5);

			if (session()->get('chat_alert_time', 0) < time() - 60) {
				session()->put('chat_alert', 0);
			}
		}

		$message = str_replace(['\\', '\\\'', '\\\\', '\\&quot;'], ['', '\'', '\\', '&quot;'], $message);

		$users = [];
		$private = false;

		if (preg_match_all('/приватно \[(.*?)]/iu', $message, $match)) {
			$message = preg_replace("/приватно \[(.*?)]/u", '', $message);
			$users = array_map('trim', $match[1]);
			$private = true;
		}

		if (preg_match_all('/для \[(.*?)]/iu', $message, $match)) {
			$message = preg_replace("/для \[(.*?)]/u", '', $message);
			$users = array_map('trim', $match[1]);

			if (!empty($users)) {
				$users = array_unique(array_merge($users, $users));
			}
		}

		$stopwords = __('main.stopwords');

		if (!is_array($stopwords)) {
			$stopwords = [];
		}

		$message = trim($message);
		$message = strtr($message, $stopwords);

		$recipients = User::query()
			->select(['id', 'nickname'])
			->whereIn('nickname', $users)
			->get();

		if ((str_starts_with($message, '/kick') || str_starts_with($message, '/speak')) && $recipients->isNotEmpty() && $user->isAdmin()) {
			if (str_starts_with($message, '/speak')) {
				User::query()
					->whereKey($recipients->modelKeys())
					->update(['silence' => null]);

				ChatService::insertInChat(
					null,
					'Модератор ' . $user->nickname . ' разрешил общение пользовател(ю/ям) ' . $recipients->pluck('nickname')->implode(', ') . '.',
					false
				);
			} else {
				$time = 15;

				if (str_contains($message, '30')) {
					$time = 30;
				} elseif (str_contains($message, '60')) {
					$time = 60;
				} elseif (str_contains($message, '1440')) {
					$time = 1440;
				}

				User::query()
					->whereKey($recipients->modelKeys())
					->update(['silence' => now()->addMinutes($time)]);

				ChatService::insertInChat(
					null,
					'Модератор ' . $user->nickname . ' запретил общение пользовател(ю/ям) ' . $recipients->pluck('nickname')->implode(', ') . ' на ' . $time . ' минут.',
					false
				);

				return;
			}
		}

		$chatMessage = Chat::create([
			'user_id' => $user->id,
			'message' => $message,
			'recipients' => $recipients->modelKeys(),
			'private' => $private,
			'date' => now(),
		]);

		$parsedMessage = ChatMessageResource::make($chatMessage)->resolve();

		if ($chatMessage->private) {
			foreach ($chatMessage->recipients as $userId) {
				event(new ChatPrivateMessage($userId, $parsedMessage));
			}

			event(new ChatPrivateMessage(auth()->id(), $parsedMessage));
		} else {
			event(new ChatPublicMessage($parsedMessage));
		}
	}

	public function online()
	{
		/*
		$cookie = [];

		if (!$this->cookies->has($this->config->cookie->prefix . "_chat_sort")) {
			$cookie['chat_sort'] = 1;
		}
		if (!$this->cookies->has($this->config->cookie->prefix . "_chat_show")) {
			$cookie['chat_show'] = 1;
		}

		$sort = $this->cookies->get($this->config->cookie->prefix . "_chat_sort")->getValue();

		if ($this->request->hasQuery('sort')) {
			$sort = $this->request->get('sort', 'int');

			$cookie['chat_sort'] = $sort;
		}

		$show = $this->cookies->get($this->config->cookie->prefix . "_chat_show")->getValue();

		if ($this->request->hasQuery('show')) {
			$show = $this->request->get('show', 'int');

			$cookie['chat_show'] = $show;
		}

		if (count($cookie)) {
			foreach ($cookie as $key => $value) {
				$this->cookies->set($this->config->cookie->prefix . "_" . $key, $value);
			}

			$this->cookies->send();
		}*/

		$show = 1;
		$sort = 1;

		$users = User::query()
			->with(['tribe'])
			->whereNot('rank', 60)
			->where('online', '<', now()->addMinutes(5));

		switch ($sort) {
			case 2:
				$users->orderByDesc('name');
				break;
			case 3:
				$users->orderBy('level');
				break;
			case 4:
				$users->orderByDesc('level');
				break;
			default:
				$users->orderBy('name');
				break;
		}

		if ($show == 2) {
			$users->where('room', auth()->user()->room);
		}

		$userList = array();

		$users = $users->get();

		foreach ($users as $user) {
			$pl = [
				'id' => $user->id,
				'name' => $user->name,
				'rank' => $user->rank,
				'tribe' => $user->tribe?->name,
				'level' => $user->level,
				'battle' => $user->battle,
				'profession' => $user->profession,
				'status' => $user->status,
				'travma' => $user->travma?->utc()->toAtomString(),
				'silence' => $user->travma?->utc()->toAtomString(),
			];

			if ($user->invisible?->isFuture()) {
				$pl['name'] = 'Тень';
				$pl['rank'] = 0;
				$pl['tribe'] = '';
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
			'sort'	=> $sort,
			'show'	=> $show,
			'users'	=> $userList,
		]);
	}
}
