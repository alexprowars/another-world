<?php

namespace App\Engine\Locations;

use App\Engine\Services\ShopService;
use App\Exceptions\Exception;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Kirschbaum\PowerJoins\PowerJoinClause;
use Throwable;

class MagicShopController extends StoreController
{
	public function index()
	{
		$user = auth()->user();

		$section = request()->integer('section');

		$objects = ShopItem::query()
			->with('item')
			->where('shop_id', $this->shopId())
			->where('stock', '>', 0)
			->when(
				$section,
				fn(Builder $query) => $query->where('section_id', $section)->orderByDesc('section_id'),
			)
			->joinRelationship('item', function (PowerJoinClause $join) use ($user, $section) {
				$join->as('item')
					->when(!$section, fn(PowerJoinClause $query) => $query->where('req_level', '<=', $user->level));
			})
			->orderBy('item.req_level')
			->get();

		return Inertia::render('Map/MagicShop', [
			'section' => $section,
			'items' => ShopItemResource::collection($objects),
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
			$this->buy((int) $data['item_id']);
		} catch (Throwable $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $request->integer('section')]);
	}

	protected function buy(int $itemId)
	{
		$item = ShopItem::query()
			->where('shop_id', $this->shopId())
			->findOne($itemId);

		if (!$item) {
			throw new Exception('Предмет не найден в магазине');
		}

		$price = ShopService::buy($item);

		flash('Вы купили предмет <u>' . $item->item->title . '</u> за <u>' . $price . '</u> ' . ($item->item->credits > 0 ? 'пл.' : 'зол.'));
	}
}
