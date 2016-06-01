<?php

namespace App\Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class ErrorController extends Application
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
 
?>