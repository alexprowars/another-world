<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Models\User;
use App\Models\UserSet;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class EquipmentSetService
{
	public static function save(User $user, string $name): UserSet
	{
		return DB::transaction(function () use ($user, $name) {
			$slots = $user->slots()
				->lockForUpdate()
				->firstOrFail();

			$equipped = $user->items()
				->whereIn('id', $slots->getItemsId())
				->lockForUpdate()
				->get()
				->keyBy('id');

			$items = [];

			for ($slot = 1; $slot <= config('game.max_slots'); $slot++) {
				$item = $equipped->get($slots->{'i' . $slot});

				if ($item && !$item->bank && !$item->market && !$item->pawnshop && in_array($slot, InventoryService::itemSlots($item), true)) {
					$items['i' . $slot] = $item->id;
				}
			}

			if (empty($items)) {
				throw new Exception('Сначала наденьте вещи, которые хотите сохранить в комплект.');
			}

			return UserSet::create([
				'user_id' => $user->id,
				'name' => $name,
				'items' => $items,
			]);
		}, 3);
	}

	public static function wear(User $user, int $setId): int
	{
		return DB::transaction(function () use ($user, $setId) {
			$set = UserSet::query()
				->whereBelongsTo($user)
				->lockForUpdate()
				->find($setId);

			if (!$set) {
				throw new Exception('Такого комплекта не существует!');
			}

			$slots = $user->slots()
				->lockForUpdate()
				->firstOrFail();

			$saved = $set->items;

			$items = $user->items()
				->whereIn('id', array_merge(array_values($saved), $slots->getItemsId()))
				->orderBy('id')
				->lockForUpdate()
				->get()
				->keyBy('id');

			$user->setRelation('slots', $slots);

			$slots->clearCache();

			$selected = [];
			$skipped = 0;

			for ($slot = 1; $slot <= config('game.max_slots'); $slot++) {
				$itemId = $saved['i' . $slot] ?? null;

				if (!$itemId) {
					continue;
				}

				$item = $items->get($itemId);

				if (
					!$item
					|| in_array($itemId, $selected, true)
					|| !in_array($slot, InventoryService::itemSlots($item), true)
				) {
					$skipped++;
					continue;
				}

				$selected[$slot] = $item->id;
			}

			$time = CarbonImmutable::now();

			do {
				$previousCount = count($selected);

				$character = $user->newInstance($user->getAttributes(), true);

				$combatStats = $character->getCombatStats();

				foreach ($selected as $itemId) {
					$item = $items->get($itemId);

					if ($item->life?->lessThan($time)) {
						continue;
					}

					foreach (Vars::getStats() as $stat) {
						$combatStats->{$stat} += $item->{$stat} ?? 0;
					}
				}

				UserService::calculateStats($character, $time, false);

				foreach ($selected as $slot => $itemId) {
					if (!InventoryService::isAllowOnset($items->get($itemId), $character)) {
						unset($selected[$slot]);
						$skipped++;
					}
				}
			} while (!empty($selected) && count($selected) !== $previousCount);

			if (empty($selected)) {
				throw new Exception('В комплекте нет доступных для надевания вещей.');
			}

			InventoryService::unsetAllObject($user);

			foreach ($selected as $slot => $itemId) {
				$user->items()->whereKey($itemId)->update(['onset' => $slot]);

				$slots->{'i' . $slot} = $itemId;
			}

			$slots->save();

			DB::afterCommit(fn () => $slots->clearCache());

			return $skipped;
		}, 3);
	}

	public static function delete(User $user, int $setId): void
	{
		if (!UserSet::query()->whereBelongsTo($user)->whereKey($setId)->delete()) {
			throw new Exception('Такого комплекта не существует!');
		}
	}
}
