<?php
namespace App\Game\Controllers;

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