<?php

namespace App\Services;

use App\Engine\LogsService;
use App\Exceptions\Exception;
use App\Models\Item;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Support\Facades\DB;

class BarnService
{
	public static function canSell(UserItem $item): bool
	{
		return in_array($item->type, config('game.barn.resource_types'), true)
			&& !$item->present
			&& !$item->bank
			&& !$item->market
			&& !$item->pawnshop
			&& !$item->onset;
	}

	public static function sellPrice(UserItem $item): float
	{
		return round($item->price);
	}

	public static function sell(User $user, int $itemId): UserItem
	{
		return DB::transaction(function () use ($user, $itemId) {
			$slots = $user->slots()->lockForUpdate()->firstOrFail();

			$item = $user->items()->lockForUpdate()->find($itemId);

			if (!$item) {
				throw new Exception('Предмет не найден в Вашем рюкзаке!');
			}

			if (!self::canSell($item) || in_array($item->id, $slots->getItemsId(), true)) {
				throw new Exception('В Амбар можно сдать только доступные ресурсы и драгоценные камни.');
			}

			$price = self::sellPrice($item);
			$currency = $item->price_type == 1 ? 'credits' : 'gold';

			$item->delete();
			$user->increment($currency, $price);

			LogsService::addItemLog(
				$user,
				'сдал',
				$item->title . ' (' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.') . ')',
				'амбар'
			);

			DB::afterCommit(fn() => $slots->clearCache());

			return $item;
		}, 3);
	}

	/** @return array{item: UserItem, price: float} */
	public static function buy(User $user, int $itemId): array
	{
		return DB::transaction(function () use ($user, $itemId) {
			$item = Item::query()
				->where('type', config('game.barn.tool_type'))
				->find($itemId);

			if (!$item) {
				throw new Exception('Инструмент не найден в Амбаре.');
			}

			$account = User::query()->lockForUpdate()->findOrFail($user->id);
			$price = $item->getPurchasePrice($account);
			$currency = $item->credits > 0 ? 'credits' : 'gold';

			if ($account->{$currency} < $price) {
				throw new Exception('Недостаточно ' . ($item->credits > 0 ? 'платины' : 'золота') . ' для покупки инструмента.');
			}

			$account->decrement($currency, $price);
			$object = InventoryService::addInInventory($account, $item);

			LogsService::addItemLog(
				$account,
				'купил',
				$item->title . ' (' . $price . ' ' . ($item->credits > 0 ? 'пл.' : 'зол.') . ')',
				'амбар'
			);

			return [
				'item' => $object,
				'price' => $price,
			];
		}, 3);
	}
}
