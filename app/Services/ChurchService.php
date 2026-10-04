<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\Models\Item;
use App\Models\Marriage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChurchService
{
	public static function marry(User $priest, string $husbandName, string $wifeName): void
	{
		self::authorize($priest);

		DB::transaction(function () use ($priest, $husbandName, $wifeName) {
			$users = User::query()
				->whereIn('name', [trim($husbandName), trim($wifeName)])
				->orderBy('id')
				->lockForUpdate()
				->get();

			$husband = $users->first(fn (User $user) => mb_strtolower($user->name) === mb_strtolower(trim($husbandName)));
			$wife = $users->first(fn (User $user) => mb_strtolower($user->name) === mb_strtolower(trim($wifeName)));

			if (!$husband || !$wife) {
				throw new Exception('Один из игроков не найден. Проверьте имена жениха и невесты.');
			}

			if ($husband->id === $wife->id) {
				throw new Exception('Нельзя заключить брак с самим собой.');
			}

			if ($husband->gender !== 'M' || $wife->gender !== 'F') {
				throw new Exception('Жених должен быть мужчиной, а невеста — женщиной.');
			}

			foreach ([$husband, $wife] as $user) {
				if (Marriage::query()->active()->forUser($user)->lockForUpdate()->first()) {
					throw new Exception('Игрок «' . $user->name . '» уже состоит в браке.');
				}
			}

			$price = config('game.church.marriage_price');

			if ($husband->credits < $price) {
				throw new Exception('У жениха недостаточно платины для заключения брака.');
			}

			$ring = Item::query()->where('code', 'weddingring')->first();

			if (!$ring) {
				throw new Exception('Обручальные кольца пока недоступны. Обратитесь к администрации.');
			}

			Marriage::create([
				'husband_id' => $husband->id,
				'wife_id' => $wife->id,
				'priest_id' => $priest->id,
				'married_at' => now(),
			]);

			$husband->decrement('credits', $price);

			InventoryService::addInInventory($husband, $ring);
			InventoryService::addInInventory($wife, $ring);
		}, 3);
	}

	public static function divorce(User $priest, string $name): void
	{
		self::authorize($priest);

		DB::transaction(function () use ($priest, $name) {
			$initiator = User::query()->where('name', trim($name))->first();

			if (!$initiator) {
				throw new Exception('Игрок не найден. Проверьте имя.');
			}

			$marriage = Marriage::query()->active()->forUser($initiator)->first();

			if (!$marriage) {
				throw new Exception('Игрок «' . $initiator->name . '» не состоит в браке.');
			}

			$spouses = User::query()
				->withTrashed()
				->whereKey([$marriage->husband_id, $marriage->wife_id])
				->orderBy('id')
				->lockForUpdate()
				->get();

			$initiator = $spouses->firstWhere('id', $initiator->id);
			$marriage = Marriage::query()->lockForUpdate()->findOrFail($marriage->id);

			if ($marriage->divorced_at) {
				throw new Exception('Этот брак уже расторгнут.');
			}

			$price = config('game.church.divorce_price');

			if (!$initiator || $initiator->gold < $price) {
				throw new Exception('У заявителя недостаточно золота для развода.');
			}

			if (!$initiator->inquisitor_check?->isFuture()) {
				throw new Exception('Перед разводом заявителю нужно пройти проверку у инквизиторов.');
			}

			$initiator->decrement('gold', $price);
			$marriage->update([
				'divorced_at' => now(),
				'divorce_priest_id' => $priest->id,
			]);

			foreach ($spouses as $spouse) {
				self::removeRings($spouse);
			}
		}, 3);
	}

	private static function authorize(User $priest): void
	{
		if (!$priest->isAdmin()) {
			throw new Exception('Заключать и расторгать браки может только администратор.');
		}

		if ($priest->room != 22) {
			throw new Exception('Для проведения обряда нужно находиться в церкви.');
		}
	}

	private static function removeRings(User $user): void
	{
		$slots = $user->slots()->lockForUpdate()->firstOrFail();
		$rings = $user->items()
			->where('code', 'weddingring')
			->lockForUpdate()
			->get();

		$ringIds = $rings->modelKeys();

		if (empty($ringIds)) {
			return;
		}

		for ($slot = 1; $slot <= config('game.max_slots'); $slot++) {
			if (in_array($slots->{'i' . $slot}, $ringIds, true)) {
				$slots->{'i' . $slot} = 0;
			}
		}

		$slots->saveOrFail();
		$user->items()->whereKey($ringIds)->delete();

		DB::afterCommit(fn () => $slots->clearCache());
	}
}
