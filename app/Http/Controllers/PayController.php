<?php

namespace App\Http\Controllers;

use Game\Controller;

/**
 * @RoutePrefix("/pay")
 * @Route("/")
 * @Route("/{action}/")
 * @Route("/{action}{params:(/.*)*}")
 * @Private
 */
class PayController extends Controller
{
	public function initialize ()
	{
		$this->tag->setTitle('Покупка платины');

		parent::initialize();
	}

    public function indexAction()
    {
		$message = '';

		$this->view->setVar('userId', $this->user->getId());
		$this->view->setVar('message', $message);
	}
}