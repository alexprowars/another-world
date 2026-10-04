<?php

namespace App\Engine\Locations;

use App\Engine\Services\InventoryService;
use App\Engine\Services\MarketService;
use App\Exceptions\Exception;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\MarketItemResource;
use App\Models\MarketItem;
use App\Models\UserItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MarketController extends LocationController
{
	public function index(): Response|RedirectResponse
	{
		$request = request();

		$user = $request->user();
		$section = $request->integer('section');

		if ($section === 100) {
			$items = InventoryService::getInventoryObjects($user, 0)
				->filter(fn (UserItem $item) => MarketService::canSell($item))
				->map(fn (UserItem $item) => [
					'item' => InventoryItemResource::make($item),
					'min_price' => MarketService::minimumPrice($item),
				])->values();
		} else {
			$query = MarketItem::query()
				->with(['item', 'user'])
				->whereHas('item', function (Builder $query) use ($section) {
					$query->where('market', true)
						->when($section > 0 && $section < 40, fn (Builder $query) => $query->where('type', $section));
				});

			if ($section === 101) {
				$query->whereBelongsTo($user)
					->latest();
			} elseif ($section > 0 && $section < 40) {
				$query->orderBy('price')
					->orderBy('id');
			} else {
				$section = 0;

				$query->latest()
					->orderByDesc('id')
					->limit(10);
			}

			$items = MarketItemResource::collection($query->get());
		}

		return Inertia::render('Map/Market', [
			'section' => $section,
			'items' => $items,
		]);
	}

	public function store()
	{
		$request = request();

		$user = $request->user();
		$section = $request->integer('section');

		$this->prepareAction($request);

		$data = $request->validate([
			'action' => ['required', 'in:sell,buy,withdraw'],
			'id' => ['required', 'integer', 'min:1'],
			'price' => ['required_if:action,sell', 'nullable', 'regex:/^\d{1,10}([.,]\d{1,2})?$/'],
		]);

		try {
			switch ($data['action']) {
				case 'sell':
					$item = MarketService::sell($user, (int) $data['id'], (float) str_replace(',', '.', $data['price']));

					flash('Предмет <u>' . e($item->title) . '</u> выставлен на продажу');

					break;
				case 'withdraw':
					$item = MarketService::withdraw($user, (int) $data['id']);

					flash('Предмет <u>' . e($item->title) . '</u> снят с продажи');

					break;
				case 'buy':
					$item = MarketService::buy($user, (int) $data['id']);

					flash('Вы купили предмет за <u>' . $item->price . '</u> зол.');

					break;
			}
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $section]);
	}
}