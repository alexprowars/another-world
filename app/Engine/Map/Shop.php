<?php

namespace App\Engine\Map;

use App\Engine\ShopService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use App\Models\UserItem;
use App\Services\InventoryService;
use Inertia\Inertia;
use Kirschbaum\PowerJoins\PowerJoinClause;
use Throwable;

class Shop
{
	protected $shopId = 1;
	protected $templateId = 'Shop';

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

		if ($itemId = request()->integer('sell')) {
			try {
				$this->sell($itemId);
			} catch (Throwable $e) {
				flash($e->getMessage());
			}

			return back();
		}

		$section = request()->integer('section');

		$objects = collect();

		if ($section == 0) {
			$objects = ShopItem::query()
				->where('shop_id', $this->shopId)
				->where('stock', '>', 0)
				->joinRelationship('item', function (PowerJoinClause $join) use ($user) {
					$join->as('item')->where('req_level', '<=', $user->level);
				})
				->orderByDesc('section_id')
				->get();
		} elseif ($section < 40) {
			$objects = ShopItem::query()
				->where('shop_id', $this->shopId)
				->where('stock', '>', 0)
				->where('section_id', $section)
				->joinRelationship('item', function (PowerJoinClause $join) use ($user) {
					$join->as('item')->where('req_level', '<=', $user->level);
				})
				->orderBy('item.req_level')
				->get();
		} elseif ($section == 100) {
			$objects = InventoryService::getInventoryObjects($user)
				->filter(function (UserItem $item) {
					if ($this->shopId == 2 && ($item->type == 12 || $item->type == 22 || !$item->artifact)) {
						return false;
					} elseif ($item->type == 12 || $item->type == 14 || $item->type == 22) {
						return false;
					}

					return true;
				});

			return Inertia::render('Map/' . $this->templateId, [
				'section' => $section,
				'items' => InventoryItemResource::collection($objects),
			]);
		}

		return Inertia::render('Map/' . $this->templateId, [
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
