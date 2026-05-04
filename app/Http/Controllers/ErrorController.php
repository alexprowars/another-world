<?php

namespace App\Http\Controllers;

use Game\Controller;

/**
 * @RoutePrefix("/error")
 * @Route("/")
 * @Route("/{action}/")
 * @Route("/{action}{params:(/.*)*}")
 * @Private
 */
class ErrorController extends Controller
{
	public function initialize ()
	{

	}

	public function indexAction()
	{

	}

    public function notFoundAction()
    {
		$this->view->setMainView('404');
        $this->response->setStatusCode(404, 'Not Found');
    }
}