<?php

namespace Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */
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