<?

/**
 * @var \App\Game\Controllers\MapController $this
 */

$message = '';

$this->view->pick('shared/city/1_bank');

$otdel = $this->request->get('otdel', 'int');

if ($this->request->hasPost('donation'))
{
	$money 		= $this->request->getPost('donation');
	$comment 	= addslashes(htmlspecialchars($this->request->getPost('comment')));

	$money = (float) str_replace(',', '.', $money);

	if ($money > 0)
	{
		if ($this->user->credits >= $money)
		{
			$this->db->query("INSERT INTO game_donations (user_id, money, comment, time) values(".$this->user->getId().", '" . $money . "','" . $comment . "', ".time().")");
			$this->db->query("UPDATE game_users SET credits = credits - ".$money." WHERE id = ".$this->user->getId()."");

			$this->user->credits -= $money;

			$message = "Ваше пожертвование: ".$money." зол. принято!";
		}
		else
			$message = "Не хватает кредитов для пожертвования!";
	}
	else
		$message = "Неправильные данные!";
}

if ($this->request->hasPost('exchange'))
{
	$money = $this->request->getPost('exchange');
	$money = (float) str_replace(',', '.', $money);

	if ($money <= 0)
		$message = "Укажите сумму!";
	else
	{
		if ($this->user->f_credits >= $money)
		{
			$this->user->f_credits -= $money;
			$this->user->credits += $money * 20;

			$this->db->query("UPDATE game_users SET credits = ".$this->user->credits.", f_credits = ".$this->user->f_credits." WHERE id = ".$this->user->getId()."");

			$message = "Обмен совершен!! Вы приобрели ".($money * 20)." зол.";
		}
		else
			$message = "Недостаточно евро кредитов!";
	}
}

if ($otdel == 1)
{
	$list = $this->db->query("SELECT d.*, u.username FROM game_donations d LEFT JOIN game_users u ON u.id = d.user_id ORDER BY time DESC")->fetchAll();

	$this->view->setVar('list', $list);
}

$this->view->setVar('message', $message);
$this->view->setVar('otdel', $otdel);

?>