<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use Inertia\Inertia;

class GameController extends Controller
{
	private $message = '';

	public function initialize()
	{
		parent::initialize();

		$this->view->setMainView('frames');
		$this->assets->addJs('js/chat.js');
	}

	public function index()
	{
		return Inertia::render('Game', [
		]);

		return view('game');
	}

	public function setMessage($message = '')
	{
		$this->message = $message;
	}

	public function getMessage()
	{
		return $this->message;
	}
}
