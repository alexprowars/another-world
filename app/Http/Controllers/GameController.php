<?php

namespace App\Http\Controllers;

use App\Http\Controller;

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
