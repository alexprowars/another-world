<?php

namespace Sky\Core\Controllers;

use DirectoryIterator;
use Game\Models\User;
use PDO;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Mvc\Controller;
use Phalcon\Version;
use Sky\Core\Options;

class InstallController extends Controller
{
	public function initialize()
	{
		$this->assets->addCss('https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all');
		$this->assets->addCss('assets/metronic/global/plugins/bootstrap/css/bootstrap.min.css');
		$this->assets->addCss('assets/metronic/global/css/components.min.css');
		$this->assets->addCss('assets/metronic/global/css/plugins.min.css');
		$this->assets->addCss('assets/metronic/pages/css/install/layout.min.css');
		$this->assets->addJs('');
	}

	public function indexAction ()
	{

	}

	public function step1Action ()
	{
		$params = $this->preparePostParams();

		$required = [];

		$required[] = [
			'title'		=> 'PHP',
			'desc'		=> 'Минимально необходимая версия: 5.6',
			'current'	=> phpversion(),
			'status'	=> version_compare(phpversion(), '5.6.0', '>=')
		];

		$required[] = [
			'title'		=> 'Phalcon Framework',
			'desc'		=> 'Минимально необходимая версия: 3.0.0',
			'current'	=> Version::get(),
			'status'	=> version_compare(Version::get(), '3.0.0', '>=')
		];

		$required[] = [
			'title'		=> 'PDO',
			'desc'		=> '',
			'current'	=> (extension_loaded('pdo') ? 'Yes' : 'No'),
			'status'	=> extension_loaded('pdo')
		];

		$required[] = [
			'title'		=> 'Mysqli',
			'desc'		=> '',
			'current'	=> (extension_loaded('mysqli') ? 'Yes' : 'No'),
			'status'	=> extension_loaded('mysqli')
		];

		$required[] = [
			'title'		=> 'Mbstring',
			'desc'		=> '',
			'current'	=> (extension_loaded('mbstring') ? 'Yes' : 'No'),
			'status'	=> extension_loaded('mbstring')
		];

		$required[] = [
			'title'		=> '/app/config/',
			'desc'		=> 'Возможность записи в каталог',
			'current'	=> (is_writable(ROOT_PATH.'/app/config') ? 'Yes' : 'No'),
			'status'	=> is_writable(ROOT_PATH.'/app/config')
		];

		$required[] = [
			'title'		=> '/',
			'desc'		=> 'Возможность записи в каталог',
			'current'	=> (is_writable(ROOT_PATH) ? 'Yes' : 'No'),
			'status'	=> is_writable(ROOT_PATH)
		];

		$success = true;

		foreach ($required as $req)
		{
			if ($req['status'] === false)
			{
				$success = false;
				break;
			}
		}

		if ($this->request->isPost())
		{
			if (!$success)
				$this->flashSession->error('Ваша конфигурация сервера не подходит для установки');
			else
				return $this->response->redirect('step2/');
		}

		$this->view->setVar('params', $params);
		$this->view->setVar('required', $required);
		$this->view->setVar('success', $success);

		return true;
	}

	public function step2Action ()
	{
		$params = $this->preparePostParams();

		$success = false;

		if ($this->request->isPost())
		{
			$result = true;

			$connection = null;

			if ($this->request->hasPost('db'))
			{
				$dbSettings = $this->request->getPost('db');

				define('DB_PREFIX', (isset($dbSettings['prefix']) ? trim($dbSettings['prefix']) : ''));

				try
				{
					$connection = new Mysql([
						'host'			=> (isset($dbSettings['host']) ? trim($dbSettings['host']) : 'localhost'),
						'username'		=> (isset($dbSettings['username']) ? trim($dbSettings['username']) : ''),
						'password' 		=> (isset($dbSettings['password']) ? trim($dbSettings['password']) : ''),
						'dbname'		=> (isset($dbSettings['database']) ? trim($dbSettings['database']) : ''),
						'options'		=> [PDO::ATTR_PERSISTENT => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
					]);
				}
				catch (\Exception $e)
				{
					$this->flashSession->error($e->getMessage());
					$result = false;
				}
			}
			else
				$result = false;

			if ($connection)
			{
				$this->di->setShared('db', $connection);

				$modules = new DirectoryIterator(ROOT_PATH.'/app/modules');

				foreach ($modules as $module)
				{
					if (!$module->isDir())
						continue;

					if (file_exists($module->getRealPath().'/Install'))
					{
						if (file_exists($module->getRealPath().'/Install/base.sql'))
						{
							echo 'ok';
						}
					}
				}

				die();

				if ($this->request->hasPost('app'))
				{
					$appSettings = $this->request->getPost('app');

					if (isset($appSettings['title']) && $appSettings['title'] != '')
					{
						Options::set('site_title', $appSettings['title']);
					}

					if (isset($appSettings['email']) && $appSettings['email'] != '')
					{
						Options::set('site_email', $appSettings['email']);
					}
				}

				if ($this->request->hasPost('user'))
				{
					$userSettings = $this->request->getPost('user');

					if ($userSettings['email'] != '' && $userSettings['password'] != '' && $userSettings['password_confirm'] != '')
					{
						if ($userSettings['password'] != $userSettings['password_confirm'])
						{
							$this->flashSession->error('Пароли учетной записи администратора не совпадают');
							$result = false;
						}
						else
						{
							$user = new User();
							$user->username = 'admin';

							if ($user->create())
							{
								$connection->updateAsDict(
									DB_PREFIX."users_info",
									['password' => md5($userSettings['password'])],
									"id = ".$user->id
								);
							}
							else
							{
								$this->flashSession->error('Произошла ошибка при создании учетной записи администратора');
								$result = false;
							}
						}
					}
					else
					{
						$this->flashSession->error('Заполните данные учетной записи администратора');
						$result = false;
					}
				}
			}

			if ($result)
			{
				if (file_exists(ROOT_PATH.'/app/config/core1.ini'))
				{
					try
					{
						$file = fopen(ROOT_PATH.'/app/config/core1.ini', 'w+');

						$dbSettings = $this->request->getPost('db');

						fwrite($file, "[database]\n");
						fwrite($file, "adapter = Mysql\n");
						fwrite($file, "host = ".(isset($dbSettings['host']) ? trim($dbSettings['host']) : 'localhost')."\n");
						fwrite($file, "username = ".(isset($dbSettings['username']) ? trim($dbSettings['username']) : '')."\n");
						fwrite($file, "password = ".(isset($dbSettings['password']) ? trim($dbSettings['password']) : '')."\n");
						fwrite($file, "dbname = ".(isset($dbSettings['database']) ? trim($dbSettings['database']) : '')."\n");
						fwrite($file, "prefix = ".(isset($dbSettings['prefix']) ? trim($dbSettings['prefix']) : '')."\n\n");

						fwrite($file, "[application]\n");
						fwrite($file, "baseDir = /app/\n");
						fwrite($file, "baseUri = /\n");
						fwrite($file, "cacheDir = cache/\n");
						fwrite($file, "prophiler = 0\n");
						fwrite($file, "debug = 1\n\n");

						fwrite($file, "[app]\n");
						fwrite($file, "language = ru\n");

						fclose($file);

						$file = fopen(ROOT_PATH.'/.installed', 'w+');
						fwrite($file, time());
						fclose($file);
					}
					catch (\Exception $e)
					{
						$this->flashSession->error($e->getMessage());
						$result = false;
					}
				}
				else
				{
					$this->flashSession->error('Файл '.ROOT_PATH.'/app/config/core.ini'.' уже существует');
					$result = false;
				}
			}

			if (!$result)
			{
				$this->flashSession->error('Проверьте введенные данные и попробуйте ещё раз');
			}

			$success = $result;
		}

		$this->view->setVar('params', $params);
		$this->view->setVar('success', $success);
	}

	private function preparePostParams ()
	{
		$params = [];

		foreach ($this->request->getPost() as $key => $value)
		{
			$params[$key] = $value;
		}

		return $params;
	}
}