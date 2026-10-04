<?php

namespace App\Engine\Locations;

use App\Engine\Services\SmithyService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Models\UserItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SmithyController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.smithy.repairPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function repairPage(): Response
	{
		return $this->renderSection(1);
	}

	public function cutPage(): Response
	{
		return $this->renderSection(2);
	}

	public function engravingPage(): Response
	{
		return $this->renderSection(3);
	}

	public function insertPage(): Response
	{
		return $this->renderSection(4);
	}

	public function repair(Request $request)
	{
		$user = $request->user();

		SmithyService::finishWork($user);

		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
			'full' => ['sometimes', 'boolean'],
		]);

		try {
			$message = SmithyService::repair($user, (int) $data['id'], $request->boolean('full', true));

			flash($message);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.smithy.repairPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function engrave(Request $request)
	{
		$user = $request->user();

		SmithyService::finishWork($user);

		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
			'text' => ['required', 'string', 'max:25'],
		]);

		try {
			$message = SmithyService::engrave($user, (int) $data['id'], $data['text']);

			flash($message);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.smithy.engravingPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function upgrade(Request $request)
	{
		$user = $request->user();

		SmithyService::finishWork($user);

		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$message = SmithyService::upgrade($user, (int) $data['id']);

			flash($message);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.smithy.engravingPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function cut(Request $request)
	{
		$user = $request->user();

		SmithyService::finishWork($user);

		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$message = SmithyService::cut($user, (int) $data['id']);

			flash($message);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.smithy.cutPage', ['city' => $this->user->currentLocation()->city]);
	}

	public function insert(Request $request)
	{
		$user = $request->user();

		SmithyService::finishWork($user);

		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
			'target' => ['required', 'integer', 'min:1', 'different:id'],
		]);

		try {
			$message = SmithyService::insert($user, (int) $data['id'], (int) $data['target']);

			flash($message);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.smithy.insertPage', ['city' => $this->user->currentLocation()->city]);
	}

	/** @param int<1, 4> $section */
	private function renderSection(int $section): Response
	{
		$user = $this->user;

		SmithyService::finishWork($user);

		$items = collect();
		$targets = collect();
		$notice = null;

		if ($section === 2 && $user->profession != 3) {
			$notice = 'Огранкой может заниматься только огранщик.';
		} elseif ($section === 4 && $user->profession != 2) {
			$notice = 'Вставлять камни может только кузнец.';
		} elseif (!$user->r_date && !$user->r_type) {
			$inventory = $user->items()
				->where('bank', false)
				->where('market', false)
				->where('pawnshop', false)
				->orderByDesc('created_at')
				->get()
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
