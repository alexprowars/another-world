<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Resources\InventoryItemResource;
use App\Models\TribeLog;
use App\Models\User;
use App\Models\UserItem;
use App\Services\TribeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TribeController extends Controller
{
	public function index(Request $request): Response
	{
		$data = $request->validate(['section' => ['nullable', 'in:members,artifacts,settings,logs']]);
		$section = $data['section'] ?? 'members';
		$user = $request->user();
		$tribe = $user->tribe;
		$canWithdraw = TribeService::canWithdraw($user);
		$isLeader = TribeService::isLeader($user);

		abort_if($tribe && $section === 'settings' && !$isLeader, 403);
		abort_if($tribe && $section === 'logs' && !$canWithdraw, 403);

		$members = $tribe?->members()->orderBy('name')->get() ?? collect();
		$items = $tribe && $section === 'artifacts' ? $tribe->items()->with('user')->orderBy('id')->get() : collect();
		$logs = $tribe && $section === 'logs' ? $tribe->logs()->with('user')->latest('id')->limit(50)->get() : collect();

		return Inertia::render('Tribe', [
			'section' => $section,
			'tribe' => $tribe?->only(['id', 'name', 'about', 'laws', 'url', 'moneys']),
			'members' => $members->map(fn (User $member) => [
				...$member->only(['id', 'name', 'level', 'rank', 'tribe_rank']),
				'online' => $member->online !== null && $member->online->greaterThan(now()->subSeconds(180)),
				'tribe' => $tribe->only(['id', 'name']),
			]),
			'recruit_price' => $members->count() * TribeService::RECRUIT_PRICE,
			'ranks' => collect(TribeService::RANKS)->map(fn (string $name, int $id) => ['id' => $id, 'name' => $name])->values(),
			'permissions' => [
				'leader' => $isLeader,
				'members' => TribeService::canManageMembers($user),
				'withdraw' => $canWithdraw,
			],
			'items' => $items->map(fn (UserItem $item) => [
				'item' => InventoryItemResource::make($item),
				'holder' => $item->user?->only(['id', 'name', 'level', 'rank']),
			]),
			'logs' => $logs->map(fn (TribeLog $log) => [
				'id' => $log->id,
				'date' => $log->created_at?->format('d.m.Y H:i'),
				'user' => $log->user?->name,
				'action' => $log->action,
			]),
		]);
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'action' => ['required', 'in:add,remove,transfer,rank,deposit,withdraw,settings'],
			'name' => ['exclude_unless:action,add,remove,transfer,rank', 'required', 'string', 'max:100'],
			'rank' => ['exclude_unless:action,rank', 'required', 'integer', Rule::in(array_diff(array_keys(TribeService::RANKS), [TribeService::LEADER]))],
			'amount' => ['exclude_unless:action,deposit,withdraw', 'required', 'regex:/\A\d{1,10}([.,]\d{1,2})?\z/'],
			'about' => ['exclude_unless:action,settings', 'present', 'nullable', 'string', 'max:10000'],
			'laws' => ['exclude_unless:action,settings', 'present', 'nullable', 'string', 'max:10000'],
			'url' => ['exclude_unless:action,settings', 'present', 'nullable', 'url:http,https', 'max:255'],
		], [
			'name.required' => 'Укажите ник персонажа.',
			'amount.required' => 'Укажите сумму.',
			'amount.regex' => 'Укажите сумму с точностью до сотых.',
			'rank.in' => 'Выберите ранг из списка.',
			'url.url' => 'Укажите адрес сайта с http:// или https://.',
		]);

		$section = $data['action'] === 'settings' ? 'settings' : 'members';

		try {
			$message = match ($data['action']) {
				'deposit', 'withdraw' => TribeService::transferGold($request->user(), (float) str_replace(',', '.', $data['amount']), $data['action'] === 'withdraw'),
				'settings' => TribeService::updateInfo($request->user(), ['about' => $data['about'], 'laws' => $data['laws'], 'url' => $data['url']]),
				default => TribeService::manageMember($request->user(), $data['action'], $data['name'], (int) ($data['rank'] ?? 0)),
			};

			flash(e($message));
		} catch (Exception $e) {
			return to_route('tribe', ['section' => $section])->withErrors(['tribe' => $e->getMessage()]);
		}

		return to_route('tribe', ['section' => $section]);
	}
}
