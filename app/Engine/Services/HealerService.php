<?php

namespace App\Engine\Services;

use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Models\Craft;
use App\Models\User;
use App\Models\UserItem;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class HealerService
{
	public static function moveStat(User $user, string $from, string $to): string
	{
		if (
			!in_array($from, Vars::getStats(), true)
			|| !in_array($to, Vars::getStats(), true)
			|| $from === $to
		) {
			throw new Exception('Выберите две разные характеристики.');
		}

		return self::transaction($user, function (User $account) use ($from, $to) {
			if ($account->{$from} <= 1) {
				throw new Exception('Характеристику нельзя уменьшить ниже 1.');
			}

			$price = config('game.healer.move_stat_price');

			self::pay($account, $price);

			$account->{$from}--;
			$account->{$to}++;
			$account->saveOrFail();

			return 'Одно очко характеристики перенесено за ' . $price . ' зол.';
		});
	}

	public static function dispel(User $user): string
	{
		return self::transaction($user, function (User $account) {
			if (!$account->invisible?->isFuture()) {
				throw new Exception('На вас нет магии тени.');
			}

			$price = config('game.healer.dispel_price');

			self::pay($account, $price);

			$account->update([
				'invisible' => null,
			]);

			return 'Магия тени рассеяна за ' . $price . ' зол.';
		});
	}

	public static function craft(User $user, int $recipeId): string
	{
		return self::transaction($user, function (User $account) use ($recipeId) {
			if ($account->profession != 7) {
				throw new Exception('Сварить зелье может только алхимик.');
			}

			$recipe = Craft::query()
				->with('item')
				->whereHas('item', fn ($query) => $query->where('craft', 2))
				->find($recipeId);

			if (!$recipe || empty($recipe->ingredients)) {
				throw new Exception('Рецепт зелья не найден.');
			}

			self::consumeIngredients($account, $recipe->ingredients);

			$object = InventoryService::addInInventory($account, $recipe->item);

			LogsService::addItemLog($account, 'изготовил', $object->title, 'домик знахаря');

			return 'Вы изготовили зелье «' . $object->title . '».';
		});
	}

	/** @return Collection<int, UserItem> */
	public static function availableIngredients(User $user, bool $lock = false): Collection
	{
		$slots = $user->slots();

		if ($lock) {
			$slots->lockForUpdate();
		}

		$equipped = $slots->firstOrFail()->getItemsId();

		$query = $user->items()
			->where('bank', false)
			->where('market', false)
			->where('pawnshop', false)
			->whereNull('tribe_id')
			->where(function ($query) {
				$query->whereNull('present')->orWhere('present', 0);
			})
			->where(function ($query) {
				$query->whereNull('onset')->orWhere('onset', 0);
			})
			->where(function ($query) {
				$query->whereNull('life')->orWhere('life', '>', now());
			})
			->whereColumn('wearout', '<', 'wearout_max')
			->whereNotIn('id', $equipped)
			->orderBy('id');

		if ($lock) {
			$query->lockForUpdate();
		}

		return $query->get();
	}

	/** @param array<string, int> $ingredients */
	private static function consumeIngredients(User $user, array $ingredients): void
	{
		$inventory = self::availableIngredients($user, true)->groupBy('code');
		$ids = [];

		foreach ($ingredients as $code => $quantity) {
			$items = $inventory->get($code, collect());

			if ($quantity < 1 || $items->count() < $quantity) {
				throw new Exception('Не хватает ингредиентов для изготовления зелья.');
			}

			foreach ($items->take($quantity) as $item) {
				$ids[] = $item->id;
			}
		}

		$user->items()->whereIn('id', $ids)->delete();
	}

	public static function ensureAvailable(User $user): void
	{
		if (!$user->currentLocation()->is('healer') || $user->battle_id || $user->r_date || $user->r_type) {
			throw new Exception('Услуги доступны свободным персонажам в домике знахаря.');
		}
	}

	private static function pay(User $user, int $price): void
	{
		if ($user->gold < $price) {
			throw new Exception('Недостаточно золота.');
		}

		$user->decrement('gold', $price);
	}

	private static function transaction(User $user, Closure $action): string
	{
		return DB::transaction(function () use ($user, $action) {
			$account = User::query()->lockForUpdate()->findOrFail($user->id);

			self::ensureAvailable($account);

			return $action($account);
		}, 3);
	}
}
