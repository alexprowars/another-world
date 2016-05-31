<?
namespace App\Controllers;

use Phalcon\Mvc\Controller;
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
class GameController extends Controller
{
	private $message = '';

	public function initialize()
	{
		$this->tag->setDoctype(Tag::HTML5);
		$this->tag->setTitle($this->config->app->name);
		$this->view->setMainView('frames');

		$js = $this->assets->collection('jsHeader');
		$js->addJs('js/jquery-1.11.2.min.js');
		$js->addJs('js/main.js');
		$js->addJs('js/chat.js');

		$css = $this->assets->collection('cssHeader');
		$css->addJs('css/bootstrap.css');
		$css->addJs('css/main.css');
		$css->addJs('css/style.css');
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