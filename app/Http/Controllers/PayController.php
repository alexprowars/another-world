<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PayController extends Controller
{
	public function index(): Response
	{
		return Inertia::render('Pay');
	}
}
