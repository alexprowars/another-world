<?php

namespace App\Engine;

use App\Exceptions\Exception;
use App\Models\ShopItem;
use App\Models\UserItem;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class ShopService
{
	public static function buy(ShopItem $item)
	{
		if ($item->stock <= 0) {
			throw new Exception('Предмета нет на складе');
		}

		if (!$item->item) {
			throw new Exception('Предмет не найден!');
		}

		$user = auth()->user();

		if ($user->tutorial == 3 && $item->item->id == 817) {
			$item->item->gold = 0;

			$user->tutorial++;
			$user->update();
		}

		$price = $item->item->getPurchasePrice($user);

		if ($item->item->credits > 0) {
			if ($price > $user->credits) {
				throw new Exception('У Вас недостаточно денег для покупки предмета <u>' . $item->item->title . '</u>');
			}

			$user->credits -= $price;
		} else {
			if ($price > $user->gold) {
				throw new Exception('У Вас недостаточно денег для покупки предмета <u>' . $item->item->title . '</u>');
			}

			$user->gold -= $price;
		}

		$user->save();

		$item->stock -= 1;
		$item->save();

		InventoryService::addInInventory($user, $item->item);

		LogsService::addItemLog($user, 'купил', $item->item->title . ' (' . $price . ' ' . ($item->item->credits > 0 ? 'пл.' : 'зол.') . ')', 'гос магазин');

		return $price;
	}

	public static function sell(UserItem $item)
	{
		$user = auth()->user();

		$itemId = $item->id;

		return DB::transaction(function () use ($user, $itemId) {
			$slots = $user->slots()
				->lockForUpdate()
				->firstOrFail();

			$user->setRelation('slots', $slots);

			$item = $user->items()
				->lockForUpdate()
				->find($itemId);

			if (!$item) {
				throw new Exception('Предмет не найден в инвентаре');
			}

			if (
				$item->type == 12
				|| $item->bank
				|| $item->market
				|| $item->pawnshop
				|| $item->onset
				|| in_array($item->id, $slots->getItemsId(), true)
			) {
				throw new Exception('Предмет <u>' . $item->title . '</u> не подлежит продаже!');
			}

			if ($item->price_type == 1) {
				if (!$item->artifact) {
					$price = round($item->price * 0, 2);
				} else {
					$price = round($item->price * 0.5, 2);
				}
			} elseif ($item->type < 12) {
				$price = round(($item->price * (1 - ($item->wearout / ($item->wearout_max + 0.01)))) * 0.5, 2);
			} else {
				$price = round($item->price * 0.5, 2);
			}

			$item->delete();

			$user->increment($item->price_type == 1 ? 'credits' : 'gold', $price);

			LogsService::addItemLog(
				$user,
				'продал',
				$item->title . ' (' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.') . ')',
				'гос магазин'
			);

			DB::afterCommit(fn() => $slots->clearCache());

			return $price;
		}, 3);
	}
}
