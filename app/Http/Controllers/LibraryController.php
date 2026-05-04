<?php

namespace App\Http\Controllers;

use Game\Controller;

/**
 * @RoutePrefix("/library")
 * @Route("/")
 * @Route("/{action}/")
 * @Route("/{action}{params:(/.*)*}")
 * @Private
 */
class LibraryController extends Controller
{
	public function initialize ()
	{
		$this->tag->setTitle('Энциклопедия');

		parent::initialize();
	}

    public function indexAction()
    {
		$otdel = $this->request->get('otdel', 'int', 0);

		$this->view->setVar('otdel', $otdel);
	}
}