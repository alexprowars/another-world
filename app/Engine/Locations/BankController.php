<?php

namespace App\Engine\Locations;

use App\Engine\Services\BankService;
use App\Exceptions\Exception;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BankController extends LocationController
{
	public function index(): Response|RedirectResponse
	{
		$request = request();

		$section = $request->integer('section', 1) === 2 ? 2 : 1;

		$donations = $section === 1
			? Donation::query()->with('user:id,name')->latest()->orderByDesc('id')->limit(20)->get()
			: collect();

		return Inertia::render('Map/Bank', [
			'section' => $section,
			'exchange_rate' => BankService::exchangeRate(),
			'donations' => DonationResource::collection($donations),
		]);
	}

	public function store()
	{
		$request = request();

		$user = $request->user();

		$this->prepareAction($request);

		$data = $request->validate([
			'action' => ['required', 'in:donate,exchange'],
			'amount' => ['required', 'regex:/^\d{1,10}([.,]\d{1,2})?$/'],
			'comment' => ['nullable', 'string', 'max:100'],
		], [
			'amount.required' => 'Укажите сумму.',
			'amount.regex' => 'Укажите сумму с точностью до сотых.',
			'comment.max' => 'Пожелание должно содержать не более 100 символов.',
		]);

		$amount = (float) str_replace(',', '.', $data['amount']);

		$section = $data['action'] === 'exchange' ? 2 : 1;

		try {
			if ($data['action'] === 'donate') {
				BankService::donate($user, $amount, $data['comment'] ?? null);

				flash('Ваше пожертвование: ' . $amount . ' зол. принято!');
			} else {
				$gold = BankService::exchange($user, $amount);

				flash('Обмен совершён! Вы получили ' . $gold . ' зол.');
			}
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation(['section' => $section]);
	}
}
