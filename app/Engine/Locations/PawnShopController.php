<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\PawnShopService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Models\UserItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class PawnShopController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.pawn-shop.myItems', ['city' => $this->user->currentLocation()->city]);
	}

	public function myItems(Request $request): Response
	{
		$items = $request->user()->items()
			->where('pawnshop', true)
			->orderBy('updated_at')
			->orderBy('id')
			->get();

		return $this->renderPage('my-items', $items);
	}

	public function depositPage(Request $request): Response
	{
		$items = InventoryService::getInventoryObjects($request->user(), 0)
			->filter(fn (UserItem $item) => PawnShopService::canDeposit($item));

		return $this->renderPage('deposit', $items);
	}

	public function deposit(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$item = PawnShopService::deposit($request->user(), (int) $data['id']);
			$price = PawnShopService::depositPrice($item);

			flash('Предмет <u>' . e($item->title) . '</u> принят в залог. Вы получили ' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.pawn-shop.depositPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function withdraw(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$item = PawnShopService::withdraw($request->user(), (int) $data['id']);
			$price = PawnShopService::withdrawPrice($item);

			flash('Вы выкупили предмет <u>' . e($item->title) . '</u> за ' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.pawn-shop.myItems', ['city' => $this->user->currentLocation()->city]);
	}

	/** @param Collection<int, UserItem> $items */
	private function renderPage(string $tab, Collection $items): Response
	{
		return Inertia::render('Map/PawnShop', [
			'tab' => $tab,
			'deposit_percent' => PawnShopService::DEPOSIT_PERCENT,
			'withdraw_percent' => PawnShopService::WITHDRAW_PERCENT,
			'items' => $items->map(fn (UserItem $item) => [
				'item' => InventoryItemResource::make($item),
				'deposit_price' => PawnShopService::depositPrice($item),
				'withdraw_price' => PawnShopService::withdrawPrice($item),
			])->values(),
		]);
	}
}