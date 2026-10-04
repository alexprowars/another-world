<?php

namespace App\Engine\Locations;

use App\Engine\Services\HealerService;
use App\Engine\Services\TribeService;
use App\Exceptions\Exception;
use App\Facades\Vars;
use App\Http\Resources\ItemResource;
use App\Models\Craft;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class HealerController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.healer.services', ['city' => $this->user->currentLocation()->city]);
	}

	public function services(Request $request): Response
	{
		$user = $request->user();

		return Inertia::render('Map/Healer', [
			'tab' => 'services',
			'stats' => Vars::getStats(),
			'move_stat_price' => config('game.healer.move_stat_price'),
			'leave_tribe_price' => config('game.healer.leave_tribe_price'),
			'dispel_price' => config('game.healer.dispel_price'),
			'can_leave_tribe' => $user->tribe_id !== null && !TribeService::isLeader($user),
			'can_dispel' => $user->invisible?->isFuture() ?? false,
		]);
	}

	public function alchemy(Request $request): Response
	{
		$user = $request->user();

		$inventory = HealerService::availableIngredients($user)
			->countBy('code');

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

		return Inertia::render('Map/Healer', [
			'tab' => 'alchemy',
			'can_craft' => $user->profession == 7,
			'recipes' => $recipes,
		]);
	}

	public function moveStat(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'from' => ['required', 'string', Rule::in(Vars::getStats())],
			'to' => ['required', 'string', Rule::in(Vars::getStats()), 'different:from'],
		]);

		try {
			$message = HealerService::moveStat($request->user(), $data['from'], $data['to']);

			flash($message);
		} catch (Exception $e) {
			throw ValidationException::withMessages(['action' => $e->getMessage()]);
		}

		return to_route('city.healer.services', ['city' => $this->user->currentLocation()->city]);
	}

	public function leaveTribe(Request $request): RedirectResponse
	{
		try {
			$message = TribeService::leave($request->user());

			flash($message);
		} catch (Exception $e) {
			throw ValidationException::withMessages(['action' => $e->getMessage()]);
		}

		return to_route('city.healer.services', ['city' => $this->user->currentLocation()->city]);
	}

	public function dispel(Request $request): RedirectResponse
	{
		try {
			$message = HealerService::dispel($request->user());

			flash($message);
		} catch (Exception $e) {
			throw ValidationException::withMessages(['action' => $e->getMessage()]);
		}

		return to_route('city.healer.services', ['city' => $this->user->currentLocation()->city]);
	}

	public function craft(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$message = HealerService::craft($request->user(), (int) $data['id']);

			flash($message);
		} catch (Exception $e) {
			throw ValidationException::withMessages(['action' => $e->getMessage()]);
		}

		return to_route('city.healer.alchemy', ['city' => $this->user->currentLocation()->city]);
	}
}
