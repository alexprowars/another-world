<?php

namespace App\Services;

use App\Engine\LogsService;
use App\Events\ChatPrivateMessage;
use App\Exceptions\Exception;
use App\Http\Resources\ChatMessageResource;
use App\Models\Chat;
use App\Models\MarketItem;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Support\Facades\DB;

class MarketService
{
	public static function canSell(UserItem $item): bool
	{
		return !$item->present && !$item->onset && !$item->bank && !$item->sclad && !$item->market
			&& $item->price_type != 1 && !in_array($item->type, [12, 13, 15, 16, 17, 21, 22]);
	}

	public static function minimumPrice(UserItem $item): float
	{
		return max(0.01, round($item->price * 0.62, 2));
	}

	public static function sell(User $user, int $itemId, float $price): UserItem
	{
		return DB::transaction(function () use ($user, $itemId, $price) {
			$item = UserItem::query()->whereBelongsTo($user)->lockForUpdate()->find($itemId);

			if (!$item) {
				throw new Exception('Предмет не найден в Вашем рюкзаке!');
			}

			if (!self::canSell($item) || in_array($item->id, $user->getSlot()->getItemsId())) {
				throw new Exception('Этот предмет нельзя выставить на продажу!');
			}

			if (!is_finite($price) || $price < self::minimumPrice($item) || $price > 9999999999.99 || round($price, 2) != $price) {
				throw new Exception('Укажите цену не ниже ' . self::minimumPrice($item) . ' зол., с точностью до сотых.');
			}

			MarketItem::create([
				'user_item_id' => $item->id,
				'user_id' => $user->id,
				'price' => $price,
			]);

			$item->update(['market' => true]);
			LogsService::addItemLog($user, 'сдал', $item->title . ' (' . $price . ' зол.)', 'рынок');

			return $item;
		}, 3);
	}

	public static function withdraw(User $user, int $listingId): UserItem
	{
		return DB::transaction(function () use ($user, $listingId) {
			$listing = MarketItem::query()->whereBelongsTo($user)->lockForUpdate()->find($listingId);

			if (!$listing) {
				throw new Exception('Предмет не найден на рынке!');
			}

			$item = $listing->item()->lockForUpdate()->first();

			if (!$item || $item->user_id != $user->id) {
				throw new Exception('Предмет не найден в Вашем рюкзаке!');
			}

			$item->update(['market' => false]);
			$listing->delete();
			LogsService::addItemLog($user, 'снял', $item->title, 'рынок');

			return $item;
		}, 3);
	}

	public static function buy(User $user, int $listingId): MarketItem
	{
		return DB::transaction(function () use ($user, $listingId) {
			$listing = MarketItem::query()->lockForUpdate()->find($listingId);

			if (!$listing || $listing->user_id == $user->id) {
				throw new Exception('Предмет не найден на рынке!');
			}

			$item = $listing->item()->lockForUpdate()->first();

			if (!$item || !$item->market || $item->user_id != $listing->user_id || $item->onset || $item->present || $item->bank || $item->sclad) {
				throw new Exception('Предмет недоступен для покупки!');
			}

			$users = User::query()->whereKey([$user->id, $listing->user_id])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
			$buyer = $users->get($user->id);
			$seller = $users->get($listing->user_id);

			if (!$buyer || !$seller) {
				throw new Exception('Продавец или покупатель не найден!');
			}

			if ($buyer->gold < $listing->price) {
				throw new Exception('У Вас недостаточно золота для покупки предмета!');
			}

			$buyer->decrement('gold', $listing->price);
			$seller->increment('gold', $listing->price);
			$item->update(['user_id' => $buyer->id, 'market' => false]);
			$listing->delete();

			$description = $item->title . ' (' . $listing->price . ' зол.)';
			LogsService::addItemLog($buyer, 'купил', $description, 'рынок');
			LogsService::addItemLog($seller, 'продал', $description, 'рынок');

			$message = Chat::create([
				'message' => 'Ваш предмет <b>' . e($item->title) . '</b> куплен на рынке игроком <b>' . e($buyer->name) . '</b> за ' . $listing->price . ' зол.',
				'recipients' => [$seller->id],
				'private' => true,
				'date' => now(),
			]);

			DB::afterCommit(function () use ($seller, $message) {
				event(new ChatPrivateMessage($seller->id, ChatMessageResource::make($message)->resolve()));
			});

			return $listing;
		}, 3);
	}
}