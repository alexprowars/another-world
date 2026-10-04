<?php

namespace App\Http\Controllers;

use App\Engine\Services\MovementService;
use App\Exceptions\Exception;
use App\Http\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MovementController extends Controller
{
	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'location' => ['required', 'string', 'max:100'],
		]);

		$user = $request->user();

		try {
			MovementService::move($user, $data['location']);
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return redirect($user->currentLocation()->url());
	}
}
