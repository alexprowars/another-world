<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\PostOfficeService;
use App\Engine\Services\TransferService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\MailLetterResource;
use App\Models\MailLetter;
use App\Models\UserItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostOfficeController extends LocationController
{
	public function index(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();

		$section = $request->query('section', 'inbox');

		if (!in_array($section, ['inbox', 'sent', 'compose', 'transfers'], true)) {
			$section = 'inbox';
		}

		$letter = null;
		$draft = ['recipient' => '', 'subject' => ''];

		if ($section === 'compose' && $request->integer('reply') > 0) {
			$original = MailLetter::query()
				->with('sender:id,name')
				->where('recipient_id', $user->id)
				->findOrFail($request->integer('reply'));

			$subject = $original->subject;

			if (!str_starts_with($subject, 'Ответ: ')) {
				$subject = 'Ответ: ' . $subject;
			}

			$draft = [
				'recipient' => $original->sender->name,
				'subject' => mb_substr($subject, 0, 100),
			];
		} elseif (in_array($section, ['inbox', 'sent'], true) && $request->integer('letter') > 0) {
			$letter = MailLetter::query()
				->with(['sender:id,name', 'recipient:id,name'])
				->where($section === 'sent' ? 'sender_id' : 'recipient_id', $user->id)
				->findOrFail($request->integer('letter'));

			if ($section === 'inbox' && !$letter->read_at) {
				$letter->update(['read_at' => now()]);
			}
		}

		$letters = in_array($section, ['inbox', 'sent'], true)
			? MailLetter::query()
				->with(['sender:id,name', 'recipient:id,name'])
				->where($section === 'sent' ? 'sender_id' : 'recipient_id', $user->id)
				->orderByDesc('id')
				->paginate(15, ['id', 'sender_id', 'recipient_id', 'subject', 'read_at', 'created_at'])
				->appends(['section' => $section])
			: null;

		return Inertia::render('Map/PostOffice', [
			'section' => $section,
			'send_cost' => config('game.postoffice.send_cost'),
			'unread_count' => MailLetter::query()
				->where('recipient_id', $user->id)
				->whereNull('read_at')
				->count(),
			'letters' => $letters ? MailLetterResource::collection($letters->getCollection()) : [],
			'pagination' => $letters ? [
				'current_page' => $letters->currentPage(),
				'last_page' => $letters->lastPage(),
				'previous_url' => $letters->previousPageUrl(),
				'next_url' => $letters->nextPageUrl(),
				'total' => $letters->total(),
			] : null,
			'letter' => $letter ? array_merge(
				MailLetterResource::make($letter)->resolve(),
				['body' => $letter->body],
			) : null,
			'draft' => $draft,
			'transfer' => $section === 'transfers' ? $this->transferData($request) : null,
		]);
	}

	public function store()
	{
		$request = request();
		$user = $request->user();

		$this->prepareAction($request);

		$action = $request->validate(['action' => ['required', 'in:letter,item,gold']]);

		if ($action['action'] !== 'letter') {
			return $this->transfer($request);
		}

		$data = $request->validate([
			'recipient' => ['required', 'string', 'max:100'],
			'subject' => ['required', 'string', 'max:100'],
			'body' => ['required', 'string', 'max:5000'],
		], [
			'recipient.required' => 'Укажите имя получателя.',
			'recipient.max' => 'Имя получателя должно содержать не более 100 символов.',
			'subject.required' => 'Введите тему письма.',
			'subject.max' => 'Тема должна содержать не более 100 символов.',
			'body.required' => 'Введите текст письма.',
			'body.max' => 'Текст письма должен содержать не более 5000 символов.',
		]);

		$letter = PostOfficeService::send($user, $data['recipient'], $data['subject'], $data['body']);

		flash('Письмо успешно отправлено. Списано ' . config('game.postoffice.send_cost') . ' зол.');

		return $this->redirectToLocation(['section' => 'sent', 'letter' => $letter->id]);
	}

	private function transferData(Request $request): array
	{
		$data = $request->validate(['login' => ['nullable', 'string', 'max:100']]);
		$user = $request->user();
		$login = $data['login'] ?? '';
		$recipient = null;
		$message = null;
		$allowed = TransferService::canTransfer($user);

		if (!$allowed) {
			$message = 'Передачи разрешены только персонажам начиная с 6 уровня!';
		} elseif ($login !== '') {
			try {
				$recipient = TransferService::findRecipient($user, $login);
			} catch (Exception $e) {
				$message = $e->getMessage();
			}
		}

		$items = $recipient ? InventoryService::getInventoryObjects($user, 0) : collect();

		return [
			'login' => $login,
			'allowed' => $allowed,
			'message' => $message,
			'recipient' => $recipient ? [
				...$recipient->only(['id', 'name', 'level', 'rank']),
				'tribe' => $recipient->tribe?->only(['id', 'name']),
			] : null,
			'items' => $items->map(fn (UserItem $item) => [
				'item' => InventoryItemResource::make($item),
				'restriction' => TransferService::itemRestriction($user, $item),
			])->values(),
		];
	}

	private function transfer(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'action' => ['required', 'in:item,gold'],
			'recipient_id' => ['required', 'integer', 'min:1'],
			'item_id' => ['exclude_unless:action,item', 'required', 'integer', 'min:1'],
			'amount' => ['exclude_unless:action,gold', 'required', 'regex:/^\d{1,10}([.,]\d{1,2})?$/'],
			'comment' => ['exclude_unless:action,gold', 'required', 'string', 'max:255'],
		], [
			'amount.required' => 'Укажите сумму.',
			'amount.regex' => 'Укажите сумму с точностью до сотых.',
			'comment.required' => 'Укажите причину передачи.',
			'comment.max' => 'Причина должна содержать не более 255 символов.',
		]);

		try {
			if ($data['action'] === 'item') {
				$transfer = TransferService::transferItem(
					$request->user(),
					(int) $data['recipient_id'],
					(int) $data['item_id'],
					$request->ip(),
				);

				flash('Предмет «' . e($transfer->item_title) . '» успешно передан.');
			} else {
				$transfer = TransferService::transferGold(
					$request->user(),
					(int) $data['recipient_id'],
					(float) str_replace(',', '.', $data['amount']),
					$data['comment'],
					$request->ip(),
				);
				flash('Успешно передано ' . $transfer->gold . ' зол.');
			}
		} catch (Exception $e) {
			return $this->redirectToLocation([
				'section' => 'transfers',
				'login' => $data['recipient_id'],
			])->withErrors(['transfer' => $e->getMessage()]);
		}

		return $this->redirectToLocation(['section' => 'transfers', 'login' => $data['recipient_id']]);
	}
}
