<?

namespace Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Game\Controller;

/**
 * @RoutePrefix("/game")
 * @Route("/")
 * @Private
 */
class GameController extends Controller
{
	private $message = '';

	public function initialize()
	{
		parent::initialize();
		
		$this->view->setMainView('frames');
		$this->assets->addJs('js/chat.js');
	}

    public function indexAction()
    {

    }

	public function setMessage ($message = '')
	{
		$this->message = $message;
	}

	public function getMessage ()
	{
		return $this->message;
	}
}