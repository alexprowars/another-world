<?php

namespace App\Engine\Map;

use App\Engine\ShopService;
use App\Exceptions\Exception;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Kirschbaum\PowerJoins\PowerJoinClause;
use Throwable;

class MagicShop
{
	public function __invoke()
	{
		$user = auth()->user();

		if ($itemId = request()->integer('buy')) {
			try {
				$this->buy($itemId);
			} catch (Throwable $e) {
				flash($e->getMessage());
			}

			return back();
		}

		$section = request()->integer('section');

		$objects = ShopItem::query()
			->where('shop_id', 3)
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

	protected function buy(int $itemId)
	{
		$item = ShopItem::query()->findOne($itemId);

		if (!$item) {
			throw new Exception('Предмет не найден в магазине');
		}

		$price = ShopService::buy($item);

		flash('Вы купили предмет <u>' . $item->item->title . '</u> за <u>' . $price . '</u> ' . ($item->item->credits > 0 ? 'пл.' : 'зол.'));
	}
}
