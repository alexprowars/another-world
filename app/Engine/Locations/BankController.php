<?php

namespace App\Engine\Locations;

use App\Engine\Services\BankService;
use App\Exceptions\Exception;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BankController extends LocationController
{
	public function index(): RedirectResponse
	{
		return to_route('city.bank.donations', ['city' => $this->user->currentLocation()->city]);
	}

	public function donations(): Response
	{
		$donations = Donation::query()
			->with('user:id,name')
			->latest()
			->orderByDesc('id')
			->limit(20)
			->get();

		return Inertia::render('Map/Bank', [
			'tab' => 'donations',
			'donations' => DonationResource::collection($donations),
		]);
	}

	public function exchangePage(): Response
	{
		return Inertia::render('Map/Bank', [
			'tab' => 'exchange',
			'exchange_rate' => BankService::exchangeRate(),
		]);
	}

	public function donate(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'amount' => ['required', 'regex:/^\d{1,10}([.,]\d{1,2})?$/'],
			'comment' => ['nullable', 'string', 'max:100'],
		], [
			'amount.required' => 'Укажите сумму.',
			'amount.regex' => 'Укажите сумму с точностью до сотых.',
			'comment.max' => 'Пожелание должно содержать не более 100 символов.',
		]);

		$amount = (float) str_replace(',', '.', $data['amount']);

		try {
			BankService::donate($request->user(), $amount, $data['comment'] ?? null);

			flash('Ваше пожертвование: ' . $amount . ' зол. принято!');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.bank.donations', ['city' => $this->user->currentLocation()->city]);
	}

	public function exchange(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'amount' => ['required', 'regex:/^\d{1,10}([.,]\d{1,2})?$/'],
		], [
			'amount.required' => 'Укажите сумму.',
			'amount.regex' => 'Укажите сумму с точностью до сотых.',
		]);

		$amount = (float) str_replace(',', '.', $data['amount']);

		try {
			$gold = BankService::exchange($request->user(), $amount);

			flash('Обмен совершён! Вы получили ' . $gold . ' зол.');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return to_route('city.bank.exchangePage', ['city' => $this->user->currentLocation()->city]);
	}
}
