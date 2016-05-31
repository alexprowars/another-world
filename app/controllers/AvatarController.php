<?php
namespace App\Controllers;

class AvatarController extends ControllerBase
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

					$this->db->query("UPDATE game_users SET obraz = '".$this->user->obraz."' WHERE id = ".$this->user->getId()."");

					$message = "Образ установлен!";
				}
			}
			else
				$message = "Вы не можете установить образ!";
		}

		$this->view->setVar('message', $message);
	}
}
?>