<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\MarketService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\MarketItemResource;
use App\Models\MarketItem;
use App\Models\UserItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.market.buyPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function buyPage(Request $request): Response
	{
		$section = $request->integer('section');

		if ($section <= 0 || $section >= 40) {
			$section = 0;
		}

		$query = MarketItem::query()
			->with(['item', 'user'])
			->whereHas('item', function (Builder $query) use ($section) {
				$query->where('market', true)
					->when($section !== 0, fn (Builder $query) => $query->where('type', $section));
			});

		if ($section !== 0) {
			$query->orderBy('price')
				->orderBy('id');
		} else {
			$query->latest()
				->orderByDesc('id')
				->limit(10);
		}

		return Inertia::render('Map/Market', [
			'tab' => 'buy',
			'section' => $section,
			'items' => MarketItemResource::collection($query->get()),
		]);
	}

	public function sellPage(Request $request): Response
	{
		$items = InventoryService::getInventoryObjects($request->user(), 0)
			->filter(fn (UserItem $item) => MarketService::canSell($item))
			->map(fn (UserItem $item) => [
				'item' => InventoryItemResource::make($item),
				'min_price' => MarketService::minimumPrice($item),
			])->values();

		return Inertia::render('Map/Market', [
			'tab' => 'sell',
			'items' => $items,
		]);
	}

	public function myItems(Request $request): Response
	{
		$items = MarketItem::query()
			->with(['item', 'user'])
			->whereHas('item', fn (Builder $query) => $query->where('market', true))
			->whereBelongsTo($request->user())
			->latest()
			->get();

		return Inertia::render('Map/Market', [
			'tab' => 'my-items',
			'items' => MarketItemResource::collection($items),
		]);
	}

	public function sell(Request $request)
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
			'price' => ['required', 'regex:/^\d{1,10}([.,]\d{1,2})?$/'],
		]);

		try {
			$item = MarketService::sell($request->user(), (int) $data['id'], (float) str_replace(',', '.', $data['price']));

			flash('Предмет <u>' . e($item->title) . '</u> выставлен на продажу');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.market.sellPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function withdraw(Request $request)
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$item = MarketService::withdraw($request->user(), (int) $data['id']);

			flash('Предмет <u>' . e($item->title) . '</u> снят с продажи');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.market.myItems', ['city' => $this->user->currentLocation()->city]);
	}

	public function buy(Request $request)
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$item = MarketService::buy($request->user(), (int) $data['id']);

			flash('Вы купили предмет за <u>' . $item->price . '</u> зол.');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.market.buyPage', [
			'city' => $this->user->currentLocation()->city,
			'section' => $request->integer('section'),
		]);
	}
}