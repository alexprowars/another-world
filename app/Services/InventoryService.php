<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Models\Item;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Database\Eloquent\Builder;

class InventoryService
{
	public static function addInInventory(User $user, Item $item): UserItem
	{
		$object = new UserItem();
		$object->user()->associate($user);

		$object->fill([
			'code' => $item->code,
			'title' => $item->title,
			'price' => $item->price,
			'artifact' => $item->artifact,
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
			'about'		=> $item->about,
			'class'		=> $item->class,
			'poison'	=> $item->poison,
			'use_mana'	=> $item->use_mana,
			'magic'		=> $item->magic,
			'life'		=> $item->life > 0 ? now()->addSeconds($item->life) : null,
		]);

		$object->requirements = (object) array_filter([
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
			$slots->i16 = 0;
			$items[] = $slots->i16;
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
		$object = $user->items()->whereKey($itemId)
			->first();

		if (!$object) {
			throw new Exception('Вещь не найдена');
		}

		if (!self::isAllowOnset($object, $user)) {
			throw new Exception('Вы не можете надеть эту вещь');
		}

		$slots = $user->getSlot();

		$slot = null;

		switch ($object->type) {
			case 1:
			case 17:
				if ($slots->i3 && $object->second) {
					$slot = 5;
				} else {
					$slot = 3;
				}
				break;
			case 2:
				$slot = 4;
				break;
			case 3:
				if (!$slots->i6) {
					$slot = 6;
				} elseif (!$slots->i7) {
					$slot = 7;
				} elseif (!$slots->i8) {
					$slot = 8;
				} elseif (!$slots->i10) {
					$slot = 10;
				} elseif (!$slots->i11) {
					$slot = 11;
				} elseif (!$slots->i12) {
					$slot = 12;
				} else {
					$slot = 6;
				}
				break;
			case 4:
				$slot = 2;
				break;
			case 5:
				$slot = 5;
				break;
			case 6:
				$slot = 13;
				break;
			case 7:
				$slot = 9;
				break;
			case 8:
				$slot = 1;
				break;
			case 9:
				$slot = 15;
				break;
			case 10:
				$slot = 14;
				break;
			case 11:
				$slot = 16;
				break;
			case 12:
			case 14:
				if (!$slots->i17) {
					$slot = 17;
				} elseif (!$slots->i18) {
					$slot = 18;
				} else {
					$slot = 17;
				}
				break;
			case 18:
				$slot = 3;
				break;
			case 24:
				$slot = 21;
				break;
			case 25:
				$slot = 22;
				break;
			case 26:
				$slot = 20;
				break;
		}

		if ($slot) {
			$object->onset = $slot;
			$object->update();

			$slots->{'i' . $slot} = $object->id;
			$slots->save();
		}

		$slots->clearCache();
	}

	public static function isAllowOnset(UserItem $item, User $user): bool
	{
		$req = $item->requirements;

		if ($item->wearout >= $item->wearout_max) {
			return false;
		}

		if (isset($req['level']) && $user->level < $req['level']) {
			return false;
		}

		if (isset($req['profession']) && $user->profession != $req['profession']) {
			return false;
		}

		if (array_any(Vars::getStats(), fn($stat) => isset($req[$stat]) && $user->{$stat} < $req[$stat])) {
			return false;
		}

		if (in_array($item->type, [15, 16, 19, 20, 21, 22, 23])) {
			return false;
		}

		if ($item->life?->isPast()) {
			return false;
		}

		return true;
	}

	public static function getInventoryObjects(User $user, int $type = 1)
	{
		$result = $user->items()
			->where('bank', false)
			->where('komis', false)
			->where('sclad', false)
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

		return $result->get();
	}
}
