<?php
namespace App\Game\Controllers;

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