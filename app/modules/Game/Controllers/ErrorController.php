<?php

namespace Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */
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