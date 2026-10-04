<?php

namespace App\Engine\Locations;

use App\Engine\Services\ChurchService;
use App\Exceptions\Exception;
use App\Models\Marriage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ChurchController extends LocationController
{
	public function index(Request $request): Response|RedirectResponse
	{
		$user = $request->user();

		$marriage = Marriage::query()
			->active()
			->forUser($user)
			->with(['husband', 'wife'])
			->first();

		$spouse = $marriage
			? ($marriage->husband_id === $user->id ? $marriage->wife : $marriage->husband)
			: null;

		$history = Marriage::query()
			->with(['husband', 'wife', 'priest'])
			->orderByDesc('married_at')
			->orderByDesc('id')
			->limit(20)
			->get()
			->map(fn (Marriage $entry) => [
				'id' => $entry->id,
				'husband' => $entry->husband->only(['id', 'name']),
				'wife' => $entry->wife->only(['id', 'name']),
				'priest' => $entry->priest->only(['id', 'name']),
				'date' => $entry->married_at->format('d.m.Y H:i'),
				'divorced' => $entry->divorced_at !== null,
			]);

		return Inertia::render('Map/Church', [
			'can_officiate' => $user->isAdmin(),
			'marriage_price' => config('game.church.marriage_price'),
			'divorce_price' => config('game.church.divorce_price'),
			'spouse' => $spouse?->only(['id', 'name']),
			'married_at' => $marriage?->married_at->format('d.m.Y'),
			'history' => $history,
		]);
	}

	public function marry(Request $request)
	{
		$user = $request->user();

		abort_unless($user->isAdmin(), 403);

		$data = $request->validate([
			'husband' => ['required', 'string', 'max:100'],
			'wife' => ['required', 'string', 'max:100'],
		], [
			'husband.required' => 'Укажите имя жениха.',
			'wife.required' => 'Укажите имя невесты.',
		]);

		try {
			ChurchService::marry($user, $data['husband'], $data['wife']);

			flash('Брак заключён. Молодожёны получили обручальные кольца!');
		} catch (Exception $e) {
			throw ValidationException::withMessages(['action' => $e->getMessage()]);
		}

		return $this->redirectToLocation();
	}

	public function divorce(Request $request)
	{
		$user = $request->user();

		abort_unless($user->isAdmin(), 403);

		$data = $request->validate([
			'name' => ['required', 'string', 'max:100'],
		], [
			'name.required' => 'Укажите имя заявителя.',
		]);

		try {
			ChurchService::divorce($user, $data['name']);

			flash('Брак расторгнут. Обручальные кольца изъяты.');
		} catch (Exception $e) {
			throw ValidationException::withMessages(['action' => $e->getMessage()]);
		}

		return $this->redirectToLocation();
	}
}
