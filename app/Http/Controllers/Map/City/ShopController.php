<?php

namespace App\Http\Controllers\Map\City;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use App\Models\UserItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Kirschbaum\PowerJoins\PowerJoinClause;
use Throwable;

class ShopController extends Controller
{
	public function index(Request $request)
	{
		define('SHOP_ID', 1);

		$message = '';

		if ($request->integer('buy')) {
			try {
				$item = ShopItem::query()->findOne($request->integer('buy'));
				$item->buy();

				$message = "Вы купили предмет <u>" . $item->item->title . "</u> за <u>" . $item->item->price . "</u> " . ($item->item->credits > 0 ? 'пл.' : 'зол.');
			} catch (Throwable $e) {
				$message = $e->getMessage();
			}

			return to_route('map.city.shop');
		}

		if ($request->integer('sale')) {
			$item = UserItem::query()
				->whereBelongsTo($this->user)
				->findOne($request->integer('sale'));

			$message = $item->sale();
		}

		if ($message) {
			//throw new Exception($message);
		}

		$section = $request->integer('section');

		$objects = collect();

		if ($section == 0) {
			$objects = ShopItem::query()
				->where('shop_id', SHOP_ID)
				->where('count', '>', 0)
				->joinRelationship('item', function (PowerJoinClause $join) {
					$join->as('item')->where('min_level', $this->user->level);
				})
				->orderByDesc('section_id')
				->get();
		} elseif ($section < 40) {
			$objects = ShopItem::query()
				->where('shop_id', SHOP_ID)
				->where('count', '>', 0)
				->where('section_id', $section)
				->joinRelationship('item', function (PowerJoinClause $join) {
					$join->as('item')->where('min_level', $this->user->level);
				})
				->orderBy('item.min_level')
				->get();
		} elseif ($section == 100) {
			$objects = $this->user->getSlot()->getInventoryObjects();
		}

		return Inertia::render('Map/Shop', [
			'message' => $message,
			'section' => $section,
			'items' => ShopItemResource::collection($objects),
		]);
	}
}
