<?php
namespace App\Controllers;

class PayController extends ControllerBase
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