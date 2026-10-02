<?php

namespace App\Engine\Map;

use App\Exceptions\Exception;
use App\Models\Marriage;
use App\Services\ChurchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class Church
{
	public function __invoke(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();

		if ($request->isMethod('post')) {
			abort_unless($user->isAdmin(), 403);

			$data = $request->validate([
				'action' => ['required', 'in:marry,divorce'],
				'husband' => ['required_if:action,marry', 'nullable', 'string', 'max:100'],
				'wife' => ['required_if:action,marry', 'nullable', 'string', 'max:100'],
				'name' => ['required_if:action,divorce', 'nullable', 'string', 'max:100'],
			], [
				'husband.required_if' => 'Укажите имя жениха.',
				'wife.required_if' => 'Укажите имя невесты.',
				'name.required_if' => 'Укажите имя заявителя.',
			]);

			try {
				if ($data['action'] === 'marry') {
					ChurchService::marry($user, $data['husband'], $data['wife']);
					flash('Брак заключён. Молодожёны получили обручальные кольца!');
				} else {
					ChurchService::divorce($user, $data['name']);
					flash('Брак расторгнут. Обручальные кольца изъяты.');
				}
			} catch (Exception $e) {
				throw ValidationException::withMessages(['action' => $e->getMessage()]);
			}

			return to_route('map');
		}

		$marriage = Marriage::query()->active()->forUser($user)->with(['husband', 'wife'])->first();
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
}
