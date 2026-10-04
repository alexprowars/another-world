<?php

namespace App\Engine\Locations;

use App\Engine\Services\AdministrationService;
use App\Exceptions\Exception;
use App\Models\Tribe;
use App\Models\TribeRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdministrationController extends LocationController
{
	public function index(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();
		$section = $request->integer('section', 1);

		if (!in_array($section, [1, 2, 3, 4], true)) {
			$section = 1;
		}

		$requests = $section === 2
			? TribeRequest::query()->with('user:id,name')->latest()->orderByDesc('id')->limit(15)->get()
				->map(fn (TribeRequest $entry) => [
					'id' => $entry->id,
					'user' => $entry->user->name ?? 'Удалённый игрок',
					'status' => $entry->status,
				])
			: collect();

		return Inertia::render('Map/Administration', [
			'section' => $section,
			'request_price' => 100,
			'image_price' => 2000,
			'min_level' => 4,
			'requests' => $requests,
			'has_request' => $section === 2 && TribeRequest::query()->whereBelongsTo($user)->exists(),
			'images' => $section === 3 ? range(1, 49) : [],
			'tribes' => $section === 4 ? Tribe::query()->orderBy('id')->get(['id', 'name', 'short', 'about', 'laws']) : [],
		]);
	}

	public function store()
	{
		$request = request();
		$user = $request->user();
		$section = $request->integer('section', 1);

		if (!in_array($section, [1, 2, 3, 4], true)) {
			$section = 1;
		}

		$this->prepareAction($request);

		$data = $request->validate([
			'action' => ['required', 'in:submit,withdraw,buy_image'],
			'image' => ['required_if:action,buy_image', 'nullable', 'integer', 'min:1', 'max:49'],
		]);

		$section = $data['action'] === 'buy_image' ? 3 : 2;

		try {
			switch ($data['action']) {
				case 'submit':
					AdministrationService::submitRequest($user);
					flash('Вы подали заявку на проверку. Дождитесь действий инквизиторов.');
					break;
				case 'withdraw':
					AdministrationService::withdrawRequest($user);
					flash('Заявка отозвана.');
					break;
				case 'buy_image':
					AdministrationService::buyImage($user, (int) $data['image']);
					flash('Образ куплен!');
					break;
			}
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $section]);
	}
}
