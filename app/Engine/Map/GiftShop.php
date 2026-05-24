<?php

namespace App\Engine\Map;

use App\Engine\ShopService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserGift;
use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Throwable;

class GiftShop
{
	public function __invoke()
	{
		$user = auth()->user();

		if ($itemId = request()->integer('gift')) {
			try {
				$this->gift($itemId);
			} catch (Throwable $e) {
				flash($e->getMessage());
			}

			return back();
		}

		if ($itemId = request()->integer('buy')) {
			try {
				$this->buy($itemId);
			} catch (Throwable $e) {
				flash($e->getMessage());
			}

			return back();
		}

		$section = request()->integer('section');

		if ($section < 4) {
			$objects = ShopItem::query()
				->where('shop_id', 4)
				->where('count', '>', 0)
				->when(
					$section,
					fn(Builder $query) => $query->where('section_id', $section)
				)
				->orderBy('item.req_level')
				->get();
		} else {
			$subquery = DB::connection()
				->query()
				->where(function ($query) {
					$query->whereLike('code', '%flowers%')
						->orWhereLike('name', '%otkr%');
				})
				->orWhereIn('type', [15, 16, 17]);

			$objects = InventoryService::getInventoryObjects($user, 0, $subquery);

			return Inertia::render('Map/GiftShop', [
				'section' => $section,
				'items' => InventoryItemResource::collection($objects),
			]);
		}

		return Inertia::render('Map/GiftShop', [
			'section' => $section,
			'items' => ShopItemResource::collection($objects),
		]);
	}

	protected function gift(int $itemId)
	{
		$user = auth()->user();

		$from 	= request()->integer('from', 1);
		$name 	= Str::sanitize(request()->post('user'));

		if ($from != 1 && $from != 2 && $from != 3) {
			$from = 1;
		}

		if (!$user->tribe_id && $from == 2) {
			$from = 1;
		}

		if (empty($name)) {
			throw new Exception('Укажите логин персонажа, которому Вы хотите сделать подарок!');
		}

		$info = User::query()
			->where('name', $name)
			->first();

		if (!$info) {
			throw new Exception('Персонаж <u>' . $name . '</u> не найден!');
		}

		if ($info->is($user)) {
			throw new Exception('Нельзя подарить что-либо самому себе!');
		}

		if ($user->level < 2) {
			throw new Exception('Только начиная с 2 уровня Вы можете дарить подарки!');
		}

		$object = $user->items()
			->where('id', $itemId)
			->first();

		if ($object) {
			$exist = UserGift::query()
				->whereBelongsTo($object, 'item')
				->exists();

			if ($exist) {
				throw new Exception('Этот предмет уже был подарен ранее!');
			}

			if ($object->artifact) {
				throw new Exception('Вы не можете дарить артефакты!');
			}

			$text = htmlspecialchars(addslashes(request()->post('text', '')));

			$gift = $info->gifts()->make([
				'from' => $from,
				'text' => $text ?: null,
			]);

			$gift->item()->associate($object);

			if ($from == 1) {
				$gift->sender()->associate($user);
			} elseif ($from == 2) {
				$gift->sender()->associate($user->tribe);
			}

			$gift->save();

			$object->user()->associate($info);
			$object->present = true;
			$object->save();

			throw new Exception('Подарок передан к <u>' . $info->name . '</u>!');
		}
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
