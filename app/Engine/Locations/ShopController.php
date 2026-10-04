<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\ShopService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use App\Models\UserItem;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Kirschbaum\PowerJoins\PowerJoinClause;
use Throwable;

class ShopController extends StoreController
{
	protected $templateId = 'Shop';

	public function index()
	{
		$user = auth()->user();

		$section = request()->integer('section');

		$objects = collect();

		if ($section < 40) {
			$objects = ShopItem::query()
				->with('item')
				->where('shop_id', $this->shopId())
				->where('stock', '>', 0)
				->joinRelationship('item', function (PowerJoinClause $join) use ($user) {
					$join->as('item')->where('req_level', '<=', $user->level);
				})
				->when(
					$section !== 0,
					fn (Builder $query) => $query->where('section_id', $section),
				)
				->orderByDesc('section_id')
				->orderBy('item.req_level')
				->get();
		}

		return Inertia::render('Map/' . $this->templateId, [
			'section' => $section,
			'selling' => false,
			'items' => ShopItemResource::collection($objects),
		]);
	}

	public function selling()
	{
		$user = auth()->user();

		$objects = InventoryService::getInventoryObjects($user)
			->filter(function (UserItem $item) {
				if ($this->user->currentLocation()->is('boutique') && ($item->type == 12 || $item->type == 22 || !$item->artifact)) {
					return false;
				} elseif ($item->type == 12 || $item->type == 14 || $item->type == 22) {
					return false;
				}

				return true;
			});

		return Inertia::render('Map/' . $this->templateId, [
			'section' => 0,
			'selling' => true,
			'items' => InventoryItemResource::collection($objects),
		]);
	}

	public function store()
	{
		$request = request();

		$this->prepareAction($request);

		$data = $request->validate([
			'item_id' => ['required', 'integer', 'min:1'],
		]);

		try {
			if ($request->input('action') === 'sell') {
				$this->sell((int) $data['item_id']);
			} else {
				$this->buy((int) $data['item_id']);
			}
		} catch (Throwable $e) {
			flash($e->getMessage());
		}

		if ($request->input('action') === 'sell') {
			$location = $this->user->currentLocation();

			return redirect()->route('city.' . $location->code . '.selling', ['city' => $location->city]);
		}

		return $this->redirectToLocation(['section' => $request->integer('section')]);
	}

	protected function buy(int $itemId)
	{
		$item = ShopItem::query()
			->where('shop_id', $this->shopId())
			->whereHas('item', fn(Builder $query) => $query->where('req_level', '<=', auth()->user()->level))
			->findOne($itemId);

		if (!$item) {
			throw new Exception('Предмет не найден в магазине');
		}

		$price = ShopService::buy($item);

		flash('Вы купили предмет <u>' . $item->item->title . '</u> за <u>' . $price . '</u> ' . ($item->item->credits > 0 ? 'пл.' : 'зол.'));
	}

	protected function sell(int $itemId)
	{
		$item = UserItem::query()
			->whereBelongsTo(auth()->user())
			->findOne($itemId);

		if (!$item) {
			throw new Exception('Предмет не найден в инвентаре');
		}

		$price = ShopService::sell($item);

		flash('Вы удачно продали предмет <u>' . $item->title . '</u> за <u>' . $price . '</u> ' . ($item->price_type == 1 ? 'пл.' : 'зол.'));
	}
}
