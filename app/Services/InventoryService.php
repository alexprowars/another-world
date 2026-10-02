<?php

namespace App\Services;

use App\Engine\LogsService;
use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Models\Item;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InventoryService
{
	public static function canDrop(UserItem $item): bool
	{
		return in_array($item->type, [15, 16], true)
			&& !$item->onset && !$item->bank && !$item->market && !$item->pawnshop;
	}

	public static function drop(User $user, int $itemId): void
	{
		DB::transaction(function () use ($user, $itemId) {
			$item = $user->items()
				->lockForUpdate()
				->find($itemId);

			if (!$item) {
				throw new Exception('Предмет не найден в Вашем рюкзаке!');
			}

			if (!in_array($item->type, [15, 16], true)) {
				throw new Exception('Выбросить можно только открытки и подарки.');
			}

			if (!self::canDrop($item) || in_array($item->id, $user->getSlot()->getItemsId())) {
				throw new Exception('Этот предмет сейчас нельзя выбросить.');
			}

			$item->delete();

			LogsService::addItemLog($user, 'выбросил', $item->title . ' (ID: ' . $item->id . ')', 'рюкзак');
		}, 3);
	}

	public static function addInInventory(User $user, Item $item): UserItem
	{
		$object = new UserItem();
		$object->user()->associate($user);

		$object->fill([
			'code' => $item->code,
			'title' => $item->title,
			'price' => $item->credits > 0 ? $item->credits : $item->gold,
			'price_type' => $item->credits > 0 ? 1 : 0,
			'artifact' => $item->artifact,
			'tribe_id' => $item->tribe_id,
			'wearout' => 0,
			'wearout_max' => $item->wearout,
			'second' => $item->isSecondHand(),
			'type'		=> $item->type,
			'armor1'	=> $item->armor1,
			'armor2'	=> $item->armor2,
			'armor3'	=> $item->armor3,
			'armor4'	=> $item->armor4,
			'armor5'	=> $item->armor5,
			'min'		=> $item->min,
			'max'		=> $item->max,
			'hp'		=> $item->hp,
			'energy'	=> $item->energy,
			'strength'	=> $item->strength,
			'dexterity'	=> $item->dexterity,
			'agility'	=> $item->agility,
			'vitality'	=> $item->vitality,
			'intelligence' => $item->intelligence,
			'krit'		=> $item->krit,
			'mkrit'		=> $item->mkrit,
			'unkrit'	=> $item->unkrit,
			'uv'		=> $item->uv,
			'unuv'		=> $item->unuv,
			'pblock'	=> $item->pblock,
			'mblock'	=> $item->mblock,
			'pbr'		=> $item->pbr,
			'kbr'		=> $item->kbr,
			'about'		=> $item->about,
			'class'		=> $item->class,
			'poison'	=> $item->poison,
			'use_mana'	=> $item->use_mana,
			'magic'		=> $item->magic,
			'life'		=> $item->life > 0 ? now()->addSeconds($item->life) : null,
		]);

		$object->requirements = array_filter([
			'level' => $item->req_level,
			'strength' => $item->req_strength,
			'dexterity' => $item->req_dexterity,
			'agility' => $item->req_agility,
			'vitality' => $item->req_vitality,
			'intelligence' => $item->req_intelligence,
			'profession' => $item->req_profession,
		]);

		$object->saveOrFail();

		return $object;
	}

	public static function unsetAllObject(User $user)
	{
		$slots = $user->getSlot();

		$items = $slots->getItemsId();

		for ($i = 1; $i <= $slots::MAX_SLOTS; $i++) {
			$slots->{'i' . $i} = 0;
		}

		if (!empty($items) && $slots->save()) {
			$user->items()
				->whereIn('id', $items)
				->update(['onset' => null]);
		}

		$slots->clearCache();
	}

	public static function unsetObject(User $user, int $slotId)
	{
		$slots = $user->getSlot();
		$items = [];

		if (isset($slots->{'i' . $slotId})) {
			$items[] = $slots->{'i' . $slotId};
			$slots->{'i' . $slotId} = 0;
		}

		if ($slotId == 4 && $slots->i16) {
			$items[] = $slots->i16;
			$slots->i16 = 0;
		}

		if (!empty($items) && $slots->save()) {
			$user->items()
				->whereIn('id', $items)
				->update(['onset' => null]);
		}

		$slots->clearCache();
	}

	public static function onsetObject(User $user, int $itemId)
	{
		DB::transaction(function () use ($user, $itemId) {
			$slots = $user->slots()
				->lockForUpdate()
				->firstOrFail();

			$user->setRelation('slots', $slots);

			$object = $user->items()
				->lockForUpdate()
				->find($itemId);

			if (!$object) {
				throw new Exception('Вещь не найдена');
			}

			if (in_array($object->id, $slots->getItemsId(), true)) {
				return;
			}

			$onsetError = self::getOnsetError($object, $user);

			if ($onsetError !== null) {
				throw new Exception('Нельзя надеть «' . $object->title . '»: ' . $onsetError);
			}

			$availableSlots = self::itemSlots($object);

			$slot = $availableSlots[0] ?? null;

			if (in_array($object->type, [1, 17])) {
				if ($slots->i3 && $object->second) {
					$slot = 5;
				}
			} else {
				foreach ($availableSlots as $availableSlot) {
					if (!$slots->{'i' . $availableSlot}) {
						$slot = $availableSlot;
						break;
					}
				}
			}

			if (!$slot) {
				return;
			}

			$previousItemId = $slots->{'i' . $slot};

			if ($previousItemId) {
				$user->items()
					->whereKey($previousItemId)
					->update(['onset' => null]);
			}

			$object->onset = $slot;
			$object->saveOrFail();

			$slots->{'i' . $slot} = $object->id;
			$slots->saveOrFail();

			DB::afterCommit(fn() => $slots->clearCache());
		}, 3);
	}

	/** @return list<int> */
	public static function itemSlots(UserItem $item): array
	{
		return match ($item->type) {
			1, 17 => $item->second ? [3, 5] : [3],
			2 => [4],
			3 => [6, 7, 8, 10, 11, 12],
			4 => [2],
			5 => [5],
			6 => [13],
			7 => [9],
			8 => [1],
			9 => [15],
			10 => [14],
			11 => [16],
			12, 14 => [17, 18],
			18 => [3],
			24 => [21],
			25 => [22],
			26 => [20],
			default => [],
		};
	}

	public static function isAllowOnset(UserItem $item, User $user): bool
	{
		return self::getOnsetError($item, $user) === null;
	}

	private static function getOnsetError(UserItem $item, User $user): ?string
	{
		if ($item->bank || $item->market || $item->pawnshop) {
			return 'Предмет находится в хранилище, на рынке или в ломбарде.';
		}

		$req = $item->requirements;

		if ($item->wearout >= $item->wearout_max) {
			return in_array($item->type, [12, 14], true)
				? 'Использования предмета закончились.'
				: 'Предмет полностью изношен.';
		}

		if (isset($req['level']) && $user->level < $req['level']) {
			return 'Требуется уровень ' . $req['level'] . '.';
		}

		if (isset($req['profession']) && $user->profession != $req['profession']) {
			return 'Требуется профессия «' . __('main.professions.' . $req['profession']) . '».';
		}

		$combatStats = $user->getCombatStats();

		foreach (Vars::getStats() as $stat) {
			if (isset($req[$stat]) && $combatStats->{$stat} < $req[$stat]) {
				return 'Требуется характеристика «' . __('main.stats.' . $stat) . '»: ' . $req[$stat] . '.';
			}
		}

		if (in_array($item->type, [15, 16, 19, 20, 21, 22, 23])) {
			return 'Этот тип предметов нельзя надевать.';
		}

		if ($item->life?->isPast()) {
			return 'Срок действия предмета истёк.';
		}

		return null;
	}

	public static function getInventoryObjects(User $user, int $type = 1, ?\Illuminate\Database\Query\Builder $query = null)
	{
		$result = $user->items()
			->where('bank', false)
			->where('market', false)
			->where('pawnshop', false)
			->orderByDesc('created_at');

		switch ($type) {
			case 1:
				$result->where(function (Builder $query) {
					$query->where(fn(Builder $query) => $query->where('type', '>=', 1)->where('type', '<=', 11))
						->orWhere(fn(Builder $query) => $query->where('type', '>=', 24)->where('type', '<=', 25));
				});
				break;
			case 2:
				$result->where(function (Builder $query) {
					$query->where(fn(Builder $query) => $query->where('type', '>=', 12)->where('type', '<=', 13))
						->orWhere(fn(Builder $query) => $query->where('type', '>=', 26));
				});
				break;
			case 3:
				$result->where('type', 14);
				break;
			case 4:
				$result->where('type', '>=', 19)->where('type', '<=', 20);
				break;
			case 5:
				$result->where('type', 21);
				break;
			case 6:
				$result->where('type', '>=', 15)->where('type', '<=', 18);
				break;
			case 7:
				$result->where('type', 22);
				break;
			case 8:
				$result->where('type', 23);
				break;
		}

		$items = $user->getSlot()->getItemsId();

		if (!empty($items)) {
			$result->whereNotIn('id', $items);
		}

		if ($query) {
			$result->addNestedWhereQuery($query);
		}

		return $result->get();
	}
}
