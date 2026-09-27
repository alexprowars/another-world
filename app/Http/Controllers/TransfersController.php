<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Resources\InventoryItemResource;
use App\Models\UserItem;
use App\Services\InventoryService;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransfersController extends Controller
{
	public function index(Request $request): Response
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

		return Inertia::render('Transfers', [
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
		]);
	}

	public function store(Request $request): RedirectResponse
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
				$transfer = TransferService::transferItem($request->user(), (int) $data['recipient_id'], (int) $data['item_id'], $request->ip());
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
			return to_route('transfers', ['login' => $data['recipient_id']])->withErrors(['transfer' => $e->getMessage()]);
		}

		return to_route('transfers', ['login' => $data['recipient_id']]);
	}
}