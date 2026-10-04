<?php

namespace App\Engine\Locations;

use App\Engine\Services\AdministrationService;
use App\Exceptions\Exception;
use App\Models\Tribe;
use App\Models\TribeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdministrationController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.administration.registrationRules', ['city' => $this->user->currentLocation()->city]);
	}

	public function registrationRules(): Response
	{
		return Inertia::render('Map/Administration', [
			'tab' => 'rules',
			'min_level' => 4,
		]);
	}

	public function requests(Request $request): Response
	{
		$requests = TribeRequest::query()
			->with('user:id,name')
			->latest()
			->orderByDesc('id')
			->limit(15)
			->get()
			->map(fn (TribeRequest $entry) => [
				'id' => $entry->id,
				'user' => $entry->user->name ?? 'Удалённый игрок',
				'status' => $entry->status,
			]);

		return Inertia::render('Map/Administration', [
			'tab' => 'requests',
			'request_price' => 100,
			'min_level' => 4,
			'requests' => $requests,
			'has_request' => TribeRequest::query()->whereBelongsTo($request->user())->exists(),
		]);
	}

	public function images(): Response
	{
		return Inertia::render('Map/Administration', [
			'tab' => 'images',
			'image_price' => 2000,
			'images' => range(1, 49),
		]);
	}

	public function clanArchive(): Response
	{
		$tribes = Tribe::query()
			->orderBy('id')
			->get(['id', 'name', 'short', 'about', 'laws']);

		return Inertia::render('Map/Administration', [
			'tab' => 'clans',
			'tribes' => $tribes,
		]);
	}

	public function submit(): RedirectResponse
	{
		try {
			AdministrationService::submitRequest($this->user);

			flash('Вы подали заявку на проверку. Дождитесь действий инквизиторов.');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.administration.requests', ['city' => $this->user->currentLocation()->city]);
	}

	public function withdraw(): RedirectResponse
	{
		try {
			AdministrationService::withdrawRequest($this->user);

			flash('Заявка отозвана.');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.administration.requests', ['city' => $this->user->currentLocation()->city]);
	}

	public function buyImage(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'image' => ['required', 'integer', 'min:1', 'max:49'],
		]);

		try {
			AdministrationService::buyImage($this->user, (int) $data['image']);

			flash('Образ куплен!');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.administration.images', ['city' => $this->user->currentLocation()->city]);
	}
}
