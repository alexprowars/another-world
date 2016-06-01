<?

namespace App\Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Tag;

/**
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Assets\Manager assets
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Session\Adapter\Memcache session
 * @property \Phalcon\Http\Response\Cookies cookies
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Mvc\Router router
 * @property \Phalcon\Cache\Backend\Memcache cache
 * @property \App\Models\Users user
 * @property \App\Auth\Auth auth
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 */
class GameController extends Application
{
	private $message = '';

	public function initialize()
	{
		parent::initialize();
		
		$this->view->setMainView('frames');

		$js = $this->assets->collection('js');
		$js->addJs('js/chat.js');
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
?>