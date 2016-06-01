<?

namespace App\Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use App\Mail\PHPMailer;
use App\Models\Users;
use App\Models;
use Phalcon\Tag;
use Phalcon\Text;

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
 * @property \Phalcon\Mvc\Url url
 * @property \App\Models\Users user
 * @property \App\Auth\Auth auth
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \App\Game game
 */
class IndexController extends Application
{
	public function initialize()
	{
		parent::initialize();

		if (!$this->auth->isAuthorized())
		{
			$this->tag->setDocType(Tag::HTML5);
			$this->view->setMainView('index');
			$this->tag->setTitleSeparator(' | ');
			$this->tag->setTitle($this->config->app->name);

			$js = $this->assets->collection('js');
			$js->addJs('js/jquery-1.11.2.min.js');
			$js->addJs('js/index/swf.js');
			$js->addJs('js/index/common.js');

			$css = $this->assets->collection('css');
			$css->addCss('css/index/general.css');
			$css->addCss('css/index/content.css');
			$css->addCss('css/style.css');

			$totalOnline = $this->db->query("SELECT COUNT(*) AS num FROM `game_users` WHERE `onlinetime` > '".(time() - 200)."'")->fetch()['num'];
			$registeredToday = $this->db->query("SELECT COUNT(*) AS num FROM `game_users_info` WHERE `register_time` > '".startOfDay()."'")->fetch()['num'];

			$this->view->setVar('totalOnline', $totalOnline);
			$this->view->setVar('registeredToday', $registeredToday);
			$this->view->setVar('topUsers', $this->db->query("SELECT username, reit FROM game_users WHERE rank != 60 AND rank != 61 AND admin = 0 order by reit desc limit 0, 20")->fetchAll());
			$this->view->setVar('topKlans', $this->db->query("SELECT name, points FROM game_tribes order by points desc limit 10")->fetchAll());
		}
	}

	public function message ($text, $title = '')
	{
		$this->view->pick('shared/message');
		$this->view->setVar('text', $text);
		$this->view->setVar('title', $title);
		$this->view->start();

		return true;
	}

	public function loginAction ()
	{
		if ($this->request->hasPost('email') && $this->request->getPost('email') != '' && $this->request->hasPost('password') && $this->request->getPost('password') != '')
		{
			$ip = convertIp($this->request->getClientAddress());

			$ban = $this->db->query("SELECT * from game_ip_banned WHERE ip = '".$ip."'")->fetch();

			if (isset($ban['ip']))
			{
				if ($ban['end'] < time())
				{
					$this->db->query("delete from game_ip_banned WHERE ip = '".$ip."'");

					echo "<body bgcolor=EBEDEC><b><font color=red>Ошибка!</font></b><br>Ваш IP (".$this->request->getClientAddress().") был заблокирован администрацией.Срок действия блокировки прошел. Обновите страницу.";
				}
				else
				{
					echo"<body bgcolor=EBEDEC><b><font color=red>Ошибка!</font></b><br>Ваш IP (".$this->request->getClientAddress().") заблокирован администрацией
						с ".date('d-m-Y H:i', $ban['start'])." до ".date('d-m-Y H:i', $ban['end'])." по причине ".$ban['com'].".";
				}

				return false;
			}

			$login = $this->db->query("SELECT u.id, u.options_toggle, ui.password FROM game_users u, game_users_info ui WHERE ui.id = u.id AND ui.`email` = '" . $this->request->getPost('email') . "' LIMIT 1")->fetch();

			if (isset($login['id']))
			{
				if ($login['password'] == md5($this->request->getPost('password')))
				{
					$user = new Users;

					$options = $user->unpackOptions($login['options_toggle']);
					$expiretime = $this->request->hasPost("rememberme") ? (time() + 2419200) : 0;

					$this->auth->auth($login['id'], $login['password'], $options['security'], $expiretime);

					$this->response->redirect('game/');
					$this->view->disable();
				}
				else
					return $this->message('Неверный E-mail и/или пароль<br><br><a href="'.$this->url->get("index/login/").'">Назад</a>', 'Ошибка');
			}
			else
				return $this->message('Такого игрока не существует<br><br><a href="'.$this->url->get("index/login/").'">Назад</a>', 'Ошибка');
		}

		return false;
	}

	public function newsAction ()
	{
		$this->tag->prependTitle('Браузерная онлайн игра');

		$page = $this->request->getQuery('p', 'int', 1);

		$totalNews = $this->db->query("SELECT COUNT(*) AS num FROM `game_news`")->fetch()['num'];

		$np 	= 5;
		$pages 	= pagination ($totalNews, $np, '', $page);

		$news = $this->db->query("SELECT * FROM game_news ORDER BY id DESC limit ".(($page - 1) * $np).", ".$np."")->fetchAll();

		$this->view->setVar('news', $news);
		$this->view->setVar('pagination', $pages);
	}

	public function regAction ()
	{
		$this->tag->prependTitle('Регистрация');

		$message = '';
		$result = '';

		if ($this->request->hasPost('register'))
		{
			$error = 0;

			if (!preg_match("/^[А-Яа-яЁёa-zA-Z0-9_\-\!\~\.@ ]+$/", $this->request->getPost('login')))
			{
				$error = 1;
				$message = "<b>Имя персонажа</b> содержит запрещенные символы. Разрешены рус. или англ. буквы обоих регистров, цифры, символы: пробел, _, !, ~, -, .,@ <br>\n";
			}
			if (preg_match("/[a-zA-Z]/", $_POST['login']) && preg_match("/[А-Яа-яЁё]/", $this->request->getPost('login')))
			{
				$error = 1;
				$message .= "<b>Имя персонажа</b> должно состоять из букв одного алфавита<br>\n";
			}
			if (strlen($this->request->getPost('login')) < 3 || strlen($this->request->getPost('login')) > 10)
			{
				$error = 1;
				$message .= "Длина <b>Имени персонажа</b> должна быть от 3 до 10 символов<br>\n";
			}
			if (preg_match("/^(\s|_|!|~|-|\.|@)|(\s|_|!|~|-|\.|@)$/", $this->request->getPost('login')))
			{
				$error = 1;
				$message .= "<b>Имя персонажа</b> не может содержать по краям символы: пробел, _, !, ~, -, .,@<br>\n";
			}
			if (is_numeric($this->request->getPost('login')))
			{
				$error = 1;
				$message .= "<b>Имя персонажа</b> не может состоять только из цифр!<br>";
			}

			$user = $this->db->query("SELECT id FROM game_users WHERE username = '" . $this->request->getPost('login') . "'")->numRows();

			if ($user > 0)
			{
				$error = 1;
				$message .= "Персонаж с таким Именем уже существует!<br>";
			}

			if (strlen($this->request->getPost('psw')) < 6 || strlen($this->request->getPost('psw')) > 25)
			{
				$error = 1;
				$message .= "Длина пароля должна быть от 6 до 25 символов<br>\n";
			}
			if ($this->request->getPost('psw') != $this->request->getPost('conf_pass'))
			{
				$error = 1;
				$message .= "Пароль и копия пароля не совпадают";
			}

			if (!preg_match("/^[_\.0-9a-zA-Z-]{1,}@[_\.0-9a-zA-Z-]{1,}\.[_\.0-9a-zA-Z-]{2,}$/", $this->request->getPost('email')))
			{
				$error = 1;
				$message .= "Введите, пожалуйста, корректный почтовый адрес<br>\n";
			}
			if (strlen($this->request->getPost('email')) < 8 || strlen($this->request->getPost('email')) > 40)
			{
				$error = 1;
				$message .= "Длина почтового адреса должна быть от 8 до 40 знаков<br>\n";
			}

			$email = $this->db->query("SELECT `id` FROM `game_users_info` WHERE `email` = '" . $this->request->getPost('email') . "'")->numRows();

			if ($email > 0)
			{
				$error = 1;
				$message .= "Персонаж с таким e-mail уже существует!<br>";
			}

			if (!preg_match("/^[А-Яа-яЁёa-zA-Z]+$/", $this->request->getPost('name')))
			{
				$error = 1;
				$message .= "<b>Реальное имя</b> содержит запрещенные символы. Разрешены рус. или англ. буквы обоих регистров.<br>\n";
			}
			if (preg_match("/[a-zA-Z]/", $_POST['name']) && preg_match("/[А-Яа-яЁё]/", $this->request->getPost('name')))
			{
				$error = 1;
				$message .= "<b>Реальное имя</b> должно состоять из букв одного алфавита<br>\n";
			}
			if (strlen($this->request->getPost('name')) < 2 || strlen($this->request->getPost('name')) > 15)
			{
				$error = 1;
				$message .= "Длина <b>Реального имя</b> должна быть от 2 до 15 символов<br>\n";
			}
			if ($this->request->getPost('sex') != 1 && $this->request->getPost('sex') != 2)
			{
				$error = 1;
				$message .= "Выберите ваш пол";
			}
			if ($this->request->getPost('day') < 1 || $this->request->getPost('day') > 31)
			{
				$error = 1;
				$message .= "Выберите День рождения<br>";
			}
			if ($this->request->getPost('month') < 1 || $this->request->getPost('month') > 12)
			{
				$error = 1;
				$message .= "Выберите Месяц рождения<br>";
			}
			if ($this->request->getPost('year') < 1950 || $this->request->getPost('year') > 2000)
			{
				$error = 1;
				$message .= "Выберите Год рождения<br>";
			}

			if ($this->request->getPost('city') != '')
			{
				if (!preg_match("/^[А-Яа-яЁёA-Za-z0-9\"\.\\\-№]+$/", $this->request->getPost('city')))
				{
					$error = 1;
					$message .= 'В названии города могут содержаться только буквы одного алфавита (рус. или англ.), цифры, символы: пробел, ", \\, ., №, -<br>';
				}
				if (strlen($this->request->getPost('city')) < 2 || strlen($this->request->getPost('city')) > 42)
				{
					$error = 1;
					$message .= "Длина названия города должна быть от 2 до 42 знаков<br>";
				}
				if (preg_match("/[a-zA-Z]/", $this->request->getPost('city')) && preg_match("/[А-Яа-яЁё]/", $this->request->getPost('city')))
				{
					$error = 1;
					$message .= "Город</b> должн состоять из букв одного алфавита<br>";
				}
			}

			if ($this->request->getPost('deviz') != '')
			{
				if (strlen($this->request->getPost('deviz')) < 5 || strlen($this->request->getPost('deviz')) > 100)
				{
					$error = 1;
					$message .= "Длина Дивиза должна быть от 5 до 100 знаков<br>";
				}
			}

			if ($this->request->getPost('law') != 1)
			{
				$error = 1;
				$message .= "Принятие наших законов является обязательным условием!<br>";
			}
			if ($this->request->getPost('soglash') != 1)
			{
				$error = 1;
				$message .= "Принятие наших законов является обязательным условием!<br>";
			}

			if (!$error)
			{
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=6LfllwUTAAAAAIh0uFhuLAkRWVK6gypAEcPN-cSN&response=".$this->request->getPost('g-recaptcha-response')."&remoteip=".$this->request->getClientAddress()."");

				$captcha = json_decode(curl_exec($curl), true);

				curl_close($curl);

				if (!$captcha['success'])
				{
					$error = 1;
					$message .= "Неправильный регистрационный код!<br>";
				}
			}

			if (!$error)
			{
				if ($this->session->has('refid'))
					$refer = $this->session->get('refid', 'int');
				else
					$refer = 0;

				$this->db->insertAsDict(
				   	"game_users",
					array
				   	(
						'username' 	=> $this->request->getPost('login'),
						'sex' 		=> $this->request->getPost('sex', 'int'),
						'ip' 		=> convertIp($this->request->getClientAddress()),
				   	)
				);

				$iduser = $this->db->lastInsertId();

				if ($iduser > 0)
				{
					if ($refer != 0)
					{
						$ref = $this->db->query("SELECT id, username FROM game_users_info WHERE id = '".$refer."'")->fetch();

						if (isset($ref['id']))
						{
							$this->db->insertAsDict(
								"game_refs",
								array
								(
									'r_id' 	=> $iduser,
									'u_id'	=> $refer
								)
							);

							$this->game->insertInChat("Игрок под ником: <a href=\"/info/?id=" . $iduser . "\" target=_blank><b>" . $this->request->getPost('login') . "</b></a> зарегистрировался по вашей ссылке! ", $ref['name'], true);
						}
					}

					$this->db->insertAsDict(
					   	"game_users_info",
						array
					   	(
							'id' 			=> $iduser,
							'email' 		=> $this->request->getPost('email'),
							'real_city'		=> $this->request->getPost('city'),
							'deviz'			=> $this->request->getPost('deviz'),
							'birth'			=> $this->request->getPost('day', 'int') . "." . $this->request->getPost('month', 'int') . "." . $this->request->getPost('year', 'int'),
							'register_time' => time(),
							'password' 		=> md5($this->request->getPost('psw'))
					   	)
					);

					$this->db->insertAsDict(
					   	"game_slots",
						['user_id' => $iduser]
					);

					$this->db->insertAsDict(
					   	"game_users_priems",
						['user_id' => $iduser]
					);

					$this->db->insertAsDict(
					   	"game_users_journal",
						array
					   	(
							'user_id' 		=> $iduser,
							'writer' 		=> 'Регистрация',
							'message'		=> 'Зарегистрирован '.date("d.m.Y H:i:s").'. E-mail регистрации '.$this->request->getPost('email'),
							'time'			=> time(),
							'type'			=> 4
					   	)
					);

					$this->db->query("UPDATE game_config SET `value` = `value` + 1 WHERE `key` = 'totalUsers'");
					$this->config->app->totalUsers++;

					//$this->game->insertInChat("В игре зарегистрировался новый игрок под ником: <a href=\"/info/?id=" . $iduser . "\" target=_blank><b>" . $this->request->getPost('login') . "</b></a>! ", "");

					$mail = new PHPMailer();
					$mail->SetFrom($this->config->app->email, $this->config->app->name);
					$mail->AddAddress($this->request->getPost('email'));
					$mail->IsHTML(true);
					$mail->CharSet = 'utf-8';
					$mail->Subject = $this->config->app->name.": Регистрация";
					$mail->Body = "Вы успешно зарегистрировались в игре ".$this->config->app->name.".<br>Ваши данные для входа в игру:<br>Email: " . $this->request->getPost('email') . "<br>Пароль:" . $this->request->getPost('psw') . "";
					$mail->Send();

					$result = "<center><u>Спасибо за регистрацию в ".$this->config->app->name."</u><br><br>Ваши данные для входа в игру:<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Логин:&nbsp;&nbsp; <font color=660000 size=3><b>" . $this->request->getPost('email') . "</b></font><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Пароль:&nbsp;&nbsp; <font color=660000 size=3><b>" . $this->request->getPost('psw') . "</b></font><br><br>Удачной Игры!<br>";
				}
				else
					$message .= "Произошла ошибка при регистрации. Попробуйте еще раз.";
			}
		}

		$this->view->setVar('message', $message);
		$this->view->setVar('result', $result);
	}

	public function reminderAction ()
	{
		$this->tag->prependTitle('Забыли пароль?');

		$message = '';

		if ($this->request->hasQuery('id') && $this->request->hasQuery('key') && is_numeric($this->request->getQuery('id')) && intval($this->request->getQuery('id')) > 0 && $this->request->getQuery('key') != "")
		{
			$id = intval($this->request->getQuery('id'));
			$key = addslashes($this->request->getQuery('key'));

			$Lost = $this->db->query("SELECT * FROM game_lostpasswords WHERE keystring = '" . $key . "' AND user_id = '" . $id . "' AND time > " . time() . "-3600 AND active = '0' LIMIT 1;")->fetch();

			if (isset($Lost['id']))
			{
				$Mail = $this->db->query("SELECT u.username, ui.email FROM game_users u, game_users_info ui WHERE ui.id = u.id AND u.id = '" . $Lost['user_id'] . "'")->fetch();

				if (!preg_match("/^[А-Яа-яЁёa-zA-Z0-9]+$/u", $key))
					$message = 'Ошибка выборки E-mail адреса!';
				elseif (empty($Mail['email']))
					$message = 'Ошибка выборки E-mail адреса!';
				else
				{
					$NewPass = Text::random(Text::RANDOM_ALNUM, 9);

					$mail = new PHPMailer();

					$mail->IsMail();
					$mail->IsHTML(true);
					$mail->CharSet = 'utf-8';
					$mail->SetFrom($this->config->app->email, $this->config->app->name);
					$mail->AddAddress($Mail['email'], $this->config->app->name);
					$mail->Subject = 'Новый пароль в '.$this->config->app->name.'';
					$mail->Body = "Ваш новый пароль от игрового аккаунта: " . $Mail['username'] . ": " . $NewPass;
					$mail->Send();

					$this->db->query("UPDATE game_users_info SET `password` ='" . md5($NewPass) . "' WHERE `id` = '" . $id . "'");
					$this->db->query("DELETE FROM game_lostpasswords WHERE user_id = '" . $id . "'");

					$message = 'Ваш новый пароль: ' . $NewPass . '. Копия пароля отправлена на почтовый ящик!';
				}
			}
			else
				$message = 'Действие данной ссылки истекло, попробуйте пройти процедуру заново!';
		}

		if ($this->request->hasPost('email'))
		{
			$inf = $this->db->query("SELECT u.*, ui.email FROM game_users u, game_users_info ui WHERE ui.email = '".addslashes(htmlspecialchars($this->request->getPost('email')))."' AND u.id = ui.id")->fetch();

			if (isset($inf['id']))
			{
				$ip = $this->request->getClientAddress();

				$key = md5($inf['id'] . date("d-m-Y H:i:s", time()) . "ыыы");

				$this->db->insertAsDict(
				   	"game_lostpasswords",
					array
				   	(
						'user_id' 		=> $inf['id'],
						'keystring' 	=> $key,
						'time'			=> time(),
						'ip'			=> $ip,
						'active'		=> 0
				   	)
				);

				$mail = new PHPMailer();

				$mail->IsMail();
				$mail->IsHTML(true);
				$mail->CharSet = 'utf-8';
				$mail->SetFrom($this->config->app->email, $this->config->app->name);
				$mail->AddAddress($inf['email']);
				$mail->Subject = 'Восстановление забытого пароля';

				$body = "Доброго времени суток Вам!\nКто то с IP адреса " . $ip . " запросил пароль к персонажу " . $inf['username'] . " в онлайн-игре ".$this->config->app->name.".\nТак как в анкете у персонажа указан данный e-mail, то именно Вы получили это письмо.\n\n
				Для восстановления пароля перейдите по ссылке: <a href='http://".$_SERVER['HTTP_HOST']."/index/reminder/?id=" . $inf['id'] . "&key=" . $key . "'>http://".$_SERVER['HTTP_HOST']."/index/reminder/?id=" . $inf['id'] . "&key=" . $key . "</a>";

				$mail->Body = $body;

				if ($mail->Send())
					$message = 'Ссылка на восстановления пароля отправлена на ваш E-mail';
				else
					$message = 'Произошла ошибка при отправке сообщения. Обратитесь к администратору сайта за помощью.';
			}
			else
				$message = 'Персонаж не найден в базе';
		}

		$this->view->setVar('message', $message);
	}

	public function faqAction ()
	{
		$this->tag->prependTitle('FAQ');
	}

	public function startAction ()
	{
		$this->tag->prependTitle('Быстрый старт');
	}

	public function aboutAction ()
	{
		$this->tag->prependTitle('О игре');
	}

	public function lawAction ()
	{
		$this->tag->prependTitle('Законы');
	}

	public function soglashAction ()
	{
		$this->tag->prependTitle('Пользовательское соглашени');
	}

	public function terminAction ()
	{
		$this->tag->prependTitle('Термины');
	}

	public function awAction ()
	{
		$this->tag->prependTitle('Другой мир');
	}

	public function expAction ()
	{
		$this->tag->prependTitle('Таблица опыта');
		$this->view->setVar('list', $this->db->query("SELECT * FROM game_levels")->fetchAll());
	}

	public function vipAction ()
	{
		$this->tag->prependTitle('Платные Услуги');
	}

	public function ekrAction ()
	{
		$this->tag->prependTitle('Покупка Екр');
	}

	public function top_krutAction ()
	{
		$this->tag->prependTitle('Топ игроков');
		$this->view->setVar('list', $this->db->query("SELECT username,exp FROM game_users WHERE rank!=60 AND rank!=61 AND admin=0 order by exp desc limit 0,30")->fetchAll());
	}

	public function top_userAction ()
	{
		$this->tag->prependTitle('Топ крутизны');
		$this->view->setVar('list', $this->db->query("SELECT username,reit FROM game_users WHERE rank!=60 AND rank!=61 AND admin=0 order by reit desc limit 0,30")->fetchAll());
	}

	public function top_klansAction ()
	{
		$this->tag->prependTitle('Топ кланов');
	}

	public function top_refAction ()
	{
		$this->tag->prependTitle('Топ реф. ссылок');
	}


}
?>