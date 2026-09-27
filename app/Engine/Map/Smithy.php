<?php

namespace App\Engine\Map;

use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Models\UserItem;
use App\Services\SmithyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class Smithy
{
	public function __invoke(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();
		$section = $request->integer('section', 1);

		if (!in_array($section, [1, 2, 3, 4])) {
			$section = 1;
		}

		SmithyService::finishWork($user);

		if ($request->isMethod('post')) {
			$data = $request->validate([
				'action' => ['required', 'in:repair,engrave,upgrade,cut,insert'],
				'id' => ['required', 'integer', 'min:1'],
				'full' => ['sometimes', 'boolean'],
				'text' => ['required_if:action,engrave', 'nullable', 'string', 'max:25'],
				'target' => ['required_if:action,insert', 'nullable', 'integer', 'min:1', 'different:id'],
			]);

			try {
				$message = match ($data['action']) {
					'repair' => SmithyService::repair($user, (int) $data['id'], $request->boolean('full', true)),
					'engrave' => SmithyService::engrave($user, (int) $data['id'], $data['text']),
					'upgrade' => SmithyService::upgrade($user, (int) $data['id']),
					'cut' => SmithyService::cut($user, (int) $data['id']),
					'insert' => SmithyService::insert($user, (int) $data['id'], (int) $data['target']),
					default => throw new Exception('Неизвестная операция кузницы!'),
				};

				flash($message);
			} catch (Exception $e) {
				flash($e->getMessage());
			}

			return to_route('map', ['section' => $section]);
		}

		$items = collect();
		$targets = collect();
		$notice = null;

		if ($section === 2 && $user->profession != 3) {
			$notice = 'Огранкой может заниматься только огранщик.';
		} elseif ($section === 4 && $user->profession != 2) {
			$notice = 'Вставлять камни может только кузнец.';
		} elseif (!$user->r_date && !$user->r_type) {
			$inventory = $user->items()->where('bank', false)->where('market', false)->where('pawnshop', false)->orderByDesc('created_at')->get()
				->filter(fn (UserItem $item) => SmithyService::available($item));
			$equipped = $user->getSlot()->getItemsId();
			$unequipped = $inventory->filter(fn (UserItem $item) => !$item->onset && !in_array($item->id, $equipped));

			$items = match ($section) {
				1 => $inventory->filter(fn (UserItem $item) => SmithyService::canRepair($item)),
				2 => $unequipped->filter(fn (UserItem $item) => SmithyService::rawGem($item)),
				3 => $unequipped->filter(fn (UserItem $item) => SmithyService::equipment($item)),
				4 => $unequipped->filter(fn (UserItem $item) => $item->type == 20 && !SmithyService::rawGem($item)),
			};

			if ($section === 4) {
				$targets = $unequipped->filter(fn (UserItem $item) => SmithyService::equipment($item) && !$item->mf_type)->values();
			}
		}

		return Inertia::render('Map/Smithy', [
			'section' => $section,
			'notice' => $notice,
			'busy' => (bool) ($user->r_date || $user->r_type),
			'until' => $user->r_date?->toAtomString(),
			'engraving_price' => SmithyService::ENGRAVING_PRICE,
			'upgrade_price' => SmithyService::UPGRADE_PRICE,
			'work_seconds' => SmithyService::WORK_SECONDS,
			'work_stamina' => SmithyService::WORK_STAMINA,
			'items' => $items->map(fn (UserItem $item) => [
				'item' => InventoryItemResource::make($item),
				'repair_price' => SmithyService::repairPrice($item),
				'repair_one_price' => SmithyService::repairPrice($item, false),
			])->values(),
			'targets' => InventoryItemResource::collection($targets),
		]);
	}
}