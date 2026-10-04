<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use Illuminate\Http\RedirectResponse;

class WorldController extends Controller
{
	public function index(): RedirectResponse
	{
		return redirect($this->user->currentLocation()->url());
	}
}
