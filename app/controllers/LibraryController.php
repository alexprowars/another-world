<?php
namespace App\Controllers;

class LibraryController extends ControllerBase
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