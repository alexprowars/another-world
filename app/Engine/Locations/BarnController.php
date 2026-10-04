<?php

namespace App\Engine\Locations;

use App\Engine\Services\BarnService;
use App\Engine\Services\InventoryService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\UserItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BarnController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.barn.resources', ['city' => $this->user->currentLocation()->city]);
	}

	public function resources(Request $request): Response
	{
		$query = DB::query()->whereIn('type', config('game.barn.resource_types'));

		$items = InventoryService::getInventoryObjects($request->user(), 0, $query)
			->filter(fn (UserItem $item) => BarnService::canSell($item))
			->map(fn (UserItem $item) => [
				'item' => InventoryItemResource::make($item),
				'price' => BarnService::sellPrice($item),
			])
			->values();

		return Inertia::render('Map/Barn', [
			'tab' => 'resources',
			'items' => $items,
		]);
	}

	public function tools(Request $request): Response
	{
		$user = $request->user();

		$items = Item::query()
			->where('type', config('game.barn.tool_type'))
			->orderBy('req_level')
			->orderBy('title')
			->get()
			->map(fn (Item $item) => [
				'id' => $item->id,
				'item' => ItemResource::make($item),
				'price' => $item->getPurchasePrice($user),
			]);

		return Inertia::render('Map/Barn', [
			'tab' => 'tools',
			'items' => $items,
		]);
	}

	public function sell(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$item = BarnService::sell($request->user(), (int) $data['id']);
			$price = BarnService::sellPrice($item);

			flash('Вы сдали <u>' . e($item->title) . '</u> за <u>' . $price . '</u> ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.barn.resources', ['city' => $this->user->currentLocation()->city]);
	}

	public function buy(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			['item' => $item, 'price' => $price] = BarnService::buy($request->user(), (int) $data['id']);

			flash('Вы купили <u>' . e($item->title) . '</u> за <u>' . $price . '</u> ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.barn.tools', ['city' => $this->user->currentLocation()->city]);
	}
}
