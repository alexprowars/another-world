<?php

namespace App\Services;

use App\Engine\LogsService;
use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Support\Facades\DB;

class PawnShopService
{
	public const int DEPOSIT_PERCENT = 60;
	public const int WITHDRAW_PERCENT = 70;

	public static function canDeposit(UserItem $item): bool
	{
		return !$item->present && !$item->onset && !$item->bank && !$item->market && !$item->pawnshop;
	}

	public static function depositPrice(UserItem $item): float
	{
		return round($item->price * self::DEPOSIT_PERCENT / 100, 2);
	}

	public static function withdrawPrice(UserItem $item): float
	{
		return round($item->price * self::WITHDRAW_PERCENT / 100, 2);
	}

	public static function deposit(User $user, int $itemId): UserItem
	{
		return DB::transaction(function () use ($user, $itemId) {
			$item = UserItem::query()
				->whereBelongsTo($user)
				->lockForUpdate()
				->find($itemId);

			if (!$item) {
				throw new Exception('Предмет не найден в Вашем рюкзаке!');
			}

			if (!self::canDeposit($item) || in_array($item->id, $user->getSlot()->getItemsId())) {
				throw new Exception('Этот предмет нельзя заложить в ломбард!');
			}

			$account = User::query()
				->lockForUpdate()
				->findOrFail($user->id);

			$item->pawnshop = true;
			$item->save();

			$price = self::depositPrice($item);
			$currency = $item->price_type == 1 ? 'credits' : 'gold';

			$account->update([
				$currency => round($account->{$currency} + $price, 2)
			]);

			LogsService::addItemLog($account, 'сдал', $item->title . ' (' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.') . ')', 'ломбард');

			return $item;
		}, 3);
	}

	public static function withdraw(User $user, int $itemId): UserItem
	{
		return DB::transaction(function () use ($user, $itemId) {
			$item = UserItem::query()
				->whereBelongsTo($user)
				->where('pawnshop', true)
				->lockForUpdate()
				->find($itemId);

			if (!$item) {
				throw new Exception('Предмет не найден среди Ваших вещей в ломбарде!');
			}

			if ($item->market || $item->bank || $item->onset || $item->present) {
				throw new Exception('Предмет недоступен для выкупа!');
			}

			$account = User::query()
				->lockForUpdate()
				->findOrFail($user->id);

			$price = self::withdrawPrice($item);
			$currency = $item->price_type == 1 ? 'credits' : 'gold';

			if ($account->{$currency} < $price) {
				throw new Exception('Недостаточно ' . ($item->price_type == 1 ? 'платины' : 'золота') . ' для выкупа предмета!');
			}

			$item->pawnshop = false;
			$item->save();

			$account->update([
				$currency => round($account->{$currency} - $price, 2)
			]);

			LogsService::addItemLog($account, 'выкупил', $item->title . ' (' . $price . ' ' . ($item->price_type == 1 ? 'пл.' : 'зол.') . ')', 'ломбард');

			return $item;
		}, 3);
	}
}