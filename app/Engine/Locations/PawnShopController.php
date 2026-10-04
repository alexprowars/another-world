<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\PawnShopService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Models\UserItem;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PawnShopController extends LocationController
{
	public function index(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();
		$section = $request->integer('section', 50) === 100 ? 100 : 50;

		$items = $section === 100
			? InventoryService::getInventoryObjects($user, 0)->filter(fn (UserItem $item) => PawnShopService::canDeposit($item))
			: $user->items()->where('pawnshop', true)->orderBy('updated_at')->orderBy('id')->get();

		return Inertia::render('Map/PawnShop', [
			'section' => $section,
			'deposit_percent' => PawnShopService::DEPOSIT_PERCENT,
			'withdraw_percent' => PawnShopService::WITHDRAW_PERCENT,
			'items' => $items->map(fn (UserItem $item) => [
				'item' => InventoryItemResource::make($item),
				'deposit_price' => PawnShopService::depositPrice($item),
				'withdraw_price' => PawnShopService::withdrawPrice($item),
			])->values(),
		]);
	}

	public function store()
	{
		$request = request();
		$user = $request->user();
		$section = $request->integer('section', 50) === 100 ? 100 : 50;

		$this->prepareAction($request);

		$data = $request->validate([
			'action' => ['required', 'in:deposit,withdraw'],
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			if ($data['action'] === 'deposit') {
				$item = PawnShopService::deposit($user, (int) $data['id']);
				$price = PawnShopService::depositPrice($item);

				flash('Предмет <u>' . e($item->title) . '</u> принят в залог. Вы получили ' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
			} else {
				$item = PawnShopService::withdraw($user, (int) $data['id']);
				$price = PawnShopService::withdrawPrice($item);

				flash('Вы выкупили предмет <u>' . e($item->title) . '</u> за ' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
			}
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $section]);
	}
}