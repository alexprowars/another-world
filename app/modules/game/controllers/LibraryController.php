<?php
namespace App\Game\Controllers;

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