<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\PostOfficeService;
use App\Engine\Services\TransferService;
use App\Exceptions\Exception;
use App\Http\Requests\SendLetterRequest;
use App\Http\Requests\TransferGoldRequest;
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
	public function index(): RedirectResponse
	{
		return to_route('city.post-office.inbox', ['city' => $this->user->currentLocation()->city]);
	}

	public function inbox(Request $request): Response
	{
		return $this->mailbox($request, 'inbox');
	}

	public function sent(Request $request): Response
	{
		return $this->mailbox($request, 'sent');
	}

	public function compose(Request $request): Response
	{
		$draft = [
			'recipient' => '',
			'subject' => '',
		];

		if ($request->integer('reply') > 0) {
			$original = MailLetter::query()
				->with('sender:id,name')
				->where('recipient_id', $this->user->id)
				->findOrFail($request->integer('reply'));

			$subject = $original->subject;

			if (!str_starts_with($subject, 'Ответ: ')) {
				$subject = 'Ответ: ' . $subject;
			}

			$draft = [
				'recipient' => $original->sender->name,
				'subject' => mb_substr($subject, 0, 100),
			];
		}

		return $this->renderPage('compose', ['draft' => $draft]);
	}

	public function transfers(Request $request): Response
	{
		return $this->renderPage('transfers', ['transfer' => $this->transferData($request)]);
	}

	public function letter(SendLetterRequest $request): RedirectResponse
	{
		$user = $request->user();
		$data = $request->validated();

		$letter = PostOfficeService::send($user, $data['recipient'], $data['subject'], $data['body']);

		flash('Письмо успешно отправлено. Списано ' . config('game.postoffice.send_cost') . ' зол.');

		return to_route('city.post-office.sent', [
			'city' => $this->user->currentLocation()->city,
			'letter' => $letter->id,
		]);
	}

	public function item(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'recipient_id' => ['required', 'integer', 'min:1'],
			'item_id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$transfer = TransferService::transferItem(
				$request->user(),
				(int) $data['recipient_id'],
				(int) $data['item_id'],
				$request->ip(),
			);

			flash('Предмет «' . e($transfer->item_title) . '» успешно передан.');
		} catch (Exception $e) {
			return to_route('city.post-office.transfers', [
				'city' => $this->user->currentLocation()->city,
				'login' => $data['recipient_id'],
			])->withErrors(['transfer' => $e->getMessage()]);
		}

		return to_route('city.post-office.transfers', [
			'city' => $this->user->currentLocation()->city,
			'login' => $data['recipient_id'],
		]);
	}

	public function gold(TransferGoldRequest $request): RedirectResponse
	{
		$data = $request->validated();

		try {
			$transfer = TransferService::transferGold(
				$request->user(),
				(int) $data['recipient_id'],
				(float) str_replace(',', '.', $data['amount']),
				$data['comment'],
				$request->ip(),
			);

			flash('Успешно передано ' . $transfer->gold . ' зол.');
		} catch (Exception $e) {
			return to_route('city.post-office.transfers', [
				'city' => $this->user->currentLocation()->city,
				'login' => $data['recipient_id'],
			])->withErrors(['transfer' => $e->getMessage()]);
		}

		return to_route('city.post-office.transfers', [
			'city' => $this->user->currentLocation()->city,
			'login' => $data['recipient_id'],
		]);
	}

	private function mailbox(Request $request, string $section): Response
	{
		$user = $request->user();
		$letter = null;
		$userColumn = $section === 'sent' ? 'sender_id' : 'recipient_id';

		if ($request->integer('letter') > 0) {
			$letter = MailLetter::query()
				->with(['sender:id,name', 'recipient:id,name'])
				->where($userColumn, $user->id)
				->findOrFail($request->integer('letter'));

			if ($section === 'inbox' && !$letter->read_at) {
				$letter->update(['read_at' => now()]);
			}
		}

		$letters = MailLetter::query()
			->with(['sender:id,name', 'recipient:id,name'])
			->where($userColumn, $user->id)
			->orderByDesc('id')
			->paginate(15, ['id', 'sender_id', 'recipient_id', 'subject', 'read_at', 'created_at']);

		return $this->renderPage($section, [
			'letters' => MailLetterResource::collection($letters->getCollection()),
			'pagination' => [
				'current_page' => $letters->currentPage(),
				'last_page' => $letters->lastPage(),
				'previous_url' => $letters->previousPageUrl(),
				'next_url' => $letters->nextPageUrl(),
				'total' => $letters->total(),
			],
			'letter' => $letter ? array_merge(
				MailLetterResource::make($letter)->resolve(),
				['body' => $letter->body],
			) : null,
		]);
	}

	private function renderPage(string $section, array $data): Response
	{
		return Inertia::render('Map/PostOffice', [
			'section' => $section,
			'send_cost' => config('game.postoffice.send_cost'),
			'unread_count' => MailLetter::query()
				->where('recipient_id', $this->user->id)
				->whereNull('read_at')
				->count(),
			...$data,
		]);
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
}
