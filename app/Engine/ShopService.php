<?php

namespace App\Engine;

use App\Exceptions\Exception;
use App\Models\ShopItem;
use App\Models\UserItem;
use App\Services\InventoryService;

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

		if ($item->item->credits > 0) {
			$price = $item->item->credits;

			if ($user->vip == 1) {
				$price = $item->item->getVipPrice();
			}

			if ($price > $user->credits) {
				throw new Exception("У Вас недостаточно денег для покупки предмета <u>" . $item->item->title . "</u>");
			}

			$user->credits -= $price;
		} else {
			$price = $item->item->gold;

			if ($user->vip == 1) {
				$price = $item->item->getVipPrice();
			}

			if ($user->profession == 8) {
				$price = $item->item->getMerchantPrice();
			}

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

		if ($item->type == 12 || $item->user_id != $user->id) {
			throw new Exception('Предмет <u>' . $item->title . '</u> не подледжит продаже!');
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

		if ($item->price_type == 1) {
			$user->credits += $price;
		} else {
			$user->gold += $price;
		}

		$user->save();

		LogsService::addItemLog($user, 'продал', $item->title . ' (' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.') . ')', 'гос магазин');

		return $price;
	}
}
