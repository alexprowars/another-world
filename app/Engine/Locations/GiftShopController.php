<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\ShopService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\ShopItemResource;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserGift;
use App\Models\UserItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Kirschbaum\PowerJoins\PowerJoinClause;
use Throwable;

class GiftShopController extends StoreController
{
	private const array GIFT_TYPES = [15, 16, 17];

	public function index(Request $request)
	{
		$user = auth()->user();

		$section = $request->integer('section');

		if ($section < 4) {
			$objects = ShopItem::query()
				->with('item')
				->where('shop_id', $this->shopId())
				->where('stock', '>', 0)
				->when(
					$section,
					fn(Builder $query) => $query->where('section_id', $section)
				)
				->joinRelationship('item', function (PowerJoinClause $join) {
					$join->as('item');
				})
				->orderBy('item.req_level')
				->get();
		} else {
			$subquery = DB::connection()
				->query()
				->whereIn('type', self::GIFT_TYPES);

			$objects = InventoryService::getInventoryObjects($user, 0, $subquery)
				->filter(fn(UserItem $item) => $this->canGift($user, $item));

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

	public function buy(Request $request)
	{
		$data = $request->validate([
			'item_id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$item = ShopItem::query()
				->where('shop_id', $this->shopId())
				->findOne((int) $data['item_id']);

			if (!$item) {
				throw new Exception('Предмет не найден в магазине');
			}

			$price = ShopService::buy($item);

			flash('Вы купили предмет <u>' . $item->item->title . '</u> за <u>' . $price . '</u> ' . ($item->item->credits > 0 ? 'пл.' : 'зол.'));
		} catch (Throwable $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $request->integer('section')]);
	}

	public function gift(Request $request)
	{
		$data = $request->validate([
			'item_id' => ['required', 'integer', 'min:1'],
			'user' => ['required', 'string', 'max:100'],
			'from' => ['sometimes', 'integer', 'in:1,2,3'],
			'text' => ['nullable', 'string', 'max:5000'],
		]);

		try {
			$this->sendGift($request, (int) $data['item_id']);
		} catch (Throwable $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $request->integer('section')]);
	}

	private function sendGift(Request $request, int $itemId)
	{
		$user = auth()->user();

		$from 	= $request->integer('from', 1);
		$name 	= Str::sanitize($request->post('user'));

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

		DB::transaction(function () use ($request, $user, $info, $itemId, $from) {
			$object = $user->items()
				->lockForUpdate()
				->find($itemId);

			if (!$object) {
				throw new Exception('Предмет не найден в Вашем рюкзаке!');
			}

			if (!in_array($object->type, self::GIFT_TYPES, true)) {
				throw new Exception('В сувенирной лавке можно дарить только открытки, цветы и подарки. Для передачи экипировки используйте раздел передач.');
			}

			$slots = $user->slots()
				->lockForUpdate()
				->firstOrFail();

			$user->setRelation('slots', $slots);

			if (!$this->canGift($user, $object)) {
				throw new Exception('Этот предмет недоступен для подарка!');
			}

			$exist = UserGift::query()
				->whereBelongsTo($object, 'item')
				->exists();

			if ($exist) {
				throw new Exception('Этот предмет уже был подарен ранее!');
			}

			$text = strip_tags($request->string('text')->toString());

			$gift = $info->gifts()->make([
				'from' => $from,
				'text' => $text === '' ? null : $text,
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
		}, 3);

		flash('Подарок передан к <u>' . $info->name . '</u>!');
	}

	private function canGift(User $user, UserItem $item): bool
	{
		return in_array($item->type, self::GIFT_TYPES, true)
			&& !$item->artifact
			&& !$item->present
			&& !$item->bank
			&& !$item->market
			&& !$item->pawnshop
			&& !$item->onset
			&& !in_array($item->id, $user->getSlot()->getItemsId());
	}
}
