<?php

namespace App\Engine\Locations;

use App\Engine\Services\ShopService;
use App\Exceptions\Exception;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Kirschbaum\PowerJoins\PowerJoinClause;
use Throwable;

class MagicShopController extends StoreController
{
	public function index(Request $request)
	{
		$user = auth()->user();

		$section = $request->integer('section');

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

	public function buy(Request $request)
	{
		$data = $request->validate([
			'item_id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$item = ShopItem::query()
				->where('shop_id', $this->shopId())
				->findOne((int) $data['item_id']);

			if (!$item) {
				throw new Exception('Предмет не найден в магазине');
			}

			$price = ShopService::buy($item);

			flash('Вы купили предмет <u>' . $item->item->title . '</u> за <u>' . $price . '</u> ' . ($item->item->credits > 0 ? 'пл.' : 'зол.'));
		} catch (Throwable $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $request->integer('section')]);
	}
}
