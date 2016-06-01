<?php

namespace App\Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class LibraryController extends Application
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
?>