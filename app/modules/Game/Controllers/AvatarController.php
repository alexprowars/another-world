<?php

namespace Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */
use Game\Controller;

/**
 * @RoutePrefix("/avatar")
 * @Route("/")
 * @Route("/{action}/")
 * @Route("/{action}{params:(/.*)*}")
 * @Private
 */
class AvatarController extends Controller
{
	public function initialize ()
	{
		$this->tag->setTitle('Установка образа');

		parent::initialize();
	}

    public function indexAction()
    {
		$message = '';

		if ($this->request->hasQuery('setimg'))
		{
			if ($this->user->level < 8 && $this->user->obraz == '0')
			{
				if (is_numeric($this->request->getQuery('setimg')) && (intval($_GET['setimg']) > 0 && $this->request->getQuery('setimg', 'int') < 6))
				{
					$this->user->obraz = "obraz/".$this->user->sex."/".$this->request->getQuery('setimg', 'int');
					$this->user->update();

					$message = "Образ установлен!";
				}
			}
			else
				$message = "Вы не можете установить образ!";
		}

		$this->view->setVar('message', $message);
	}
}