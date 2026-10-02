<?php

namespace App\Engine\Map;

use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\UserItem;
use App\Services\BarnService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class Barn
{
	public function __invoke(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();
		$section = $request->integer('section', 1) === 2 ? 2 : 1;

		if ($request->isMethod('post')) {
			$data = $request->validate([
				'action' => ['required', 'in:sell,buy'],
				'id' => ['required', 'integer', 'min:1'],
			]);

			try {
				if ($data['action'] === 'sell') {
					$item = BarnService::sell($user, (int) $data['id']);
					$price = BarnService::sellPrice($item);

					flash('Вы сдали <u>' . e($item->title) . '</u> за <u>' . $price . '</u> ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
				} else {
					['item' => $item, 'price' => $price] = BarnService::buy($user, (int) $data['id']);

					flash('Вы купили <u>' . e($item->title) . '</u> за <u>' . $price . '</u> ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
				}
			} catch (Exception $e) {
				flash($e->getMessage());
			}

			return to_route('map', ['section' => $section]);
		}

		if ($section === 1) {
			$query = DB::query()->whereIn('type', config('game.barn.resource_types'));

			$items = InventoryService::getInventoryObjects($user, 0, $query)
				->filter(fn(UserItem $item) => BarnService::canSell($item))
				->map(fn(UserItem $item) => [
					'item' => InventoryItemResource::make($item),
					'price' => BarnService::sellPrice($item),
				])
				->values();
		} else {
			$items = Item::query()
				->where('type', config('game.barn.tool_type'))
				->orderBy('req_level')
				->orderBy('title')
				->get()
				->map(fn(Item $item) => [
					'id' => $item->id,
					'item' => ItemResource::make($item),
					'price' => $item->getPurchasePrice($user),
				]);
		}

		return Inertia::render('Map/Barn', [
			'section' => $section,
			'items' => $items,
		]);
	}
}
