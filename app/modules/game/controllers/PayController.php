<?php

namespace App\Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class PayController extends Application
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

?>