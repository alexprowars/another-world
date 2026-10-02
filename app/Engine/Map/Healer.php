<?php

namespace App\Engine\Map;

use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Http\Resources\ItemResource;
use App\Models\Craft;
use App\Models\Item;
use App\Services\HealerService;
use App\Services\TribeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class Healer
{
	public function __invoke(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();
		$section = $request->integer('section', 1);

		if (!in_array($section, [1, 2], true)) {
			$section = 1;
		}

		if ($request->isMethod('post')) {
			$data = $request->validate([
				'action' => [
					'required',
					Rule::in([
						'move_stat',
						'leave_tribe',
						'dispel',
						'craft',
					]),
				],
				'from' => ['exclude_unless:action,move_stat', 'required', 'string', Rule::in(Vars::getStats())],
				'to' => ['exclude_unless:action,move_stat', 'required', 'string', Rule::in(Vars::getStats()), 'different:from'],
				'id' => ['exclude_unless:action,craft', 'required', 'integer', 'min:1'],
			]);

			try {
				$message = match ($data['action']) {
					'move_stat' => HealerService::moveStat($user, $data['from'], $data['to']),
					'leave_tribe' => TribeService::leave($user),
					'dispel' => HealerService::dispel($user),
					'craft' => HealerService::craft($user, (int) $data['id']),
					default => throw new Exception('Неизвестная услуга знахаря.'),
				};

				flash($message);
			} catch (Exception $e) {
				throw ValidationException::withMessages(['action' => $e->getMessage()]);
			}

			return to_route('map', ['section' => $section]);
		}

		$recipes = collect();

		if ($section === 2) {
			$inventory = HealerService::availableIngredients($user)->countBy('code');

			$crafts = Craft::query()
				->with('item')
				->whereHas('item', fn ($query) => $query->where('craft', 2))
				->orderBy('id')
				->get();

			$codes = $crafts
				->flatMap(fn (Craft $craft) => array_keys($craft->ingredients))
				->unique();

			$ingredients = Item::query()
				->whereIn('code', $codes)
				->get()
				->keyBy('code');

			$recipes = $crafts->map(function (Craft $craft) use ($ingredients, $inventory) {
				$components = collect($craft->ingredients)->map(function ($quantity, $code) use ($ingredients, $inventory) {
					$ingredient = $ingredients->get($code);

					return [
						'code' => $code,
						'title' => $ingredient->title ?? $code,
						'quantity' => $quantity,
						'available' => $inventory->get($code, 0),
						'found' => $ingredient !== null,
					];
				})->values();

				$available = $components->isNotEmpty()
					&& $components->every(fn ($component) =>
						$component['found'] && $component['available'] >= $component['quantity']
					);

				return [
					'id' => $craft->id,
					'item' => ItemResource::make($craft->item),
					'ingredients' => $components,
					'available' => $available,
				];
			});
		}

		return Inertia::render('Map/Healer', [
			'section' => $section,
			'stats' => Vars::getStats(),
			'move_stat_price' => config('game.healer.move_stat_price'),
			'leave_tribe_price' => config('game.healer.leave_tribe_price'),
			'dispel_price' => config('game.healer.dispel_price'),
			'can_leave_tribe' => $user->tribe_id !== null && !TribeService::isLeader($user),
			'can_dispel' => $user->invisible?->isFuture() ?? false,
			'can_craft' => $user->profession == 7,
			'recipes' => $recipes,
		]);
	}
}
