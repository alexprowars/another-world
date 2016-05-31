<?

/**
 * @var \App\Controllers\MapController $this
 */

$otdel = $this->request->get('otdel', 'int', 0);

$message = '';

if ($this->request->hasQuery('unsale'))
{
	$unsale = (int) $this->request->getQuery('unsale', 'int');

	$item = $this->db->query("SELECT * FROM game_market WHERE id = ".$unsale." AND user_id = ".$this->user->id."")->fetch();

	if (isset($item['id']))
	{
		$object = $this->db->query("SELECT id, inf FROM game_objects WHERE id = " . $item['object_id'] . "")->fetch();

		if (isset($object['id']))
		{
			$this->db->query("UPDATE game_objects SET komis = '0' WHERE id = " . $item['object_id'] . "");
			$this->db->query("DELETE FROM game_market WHERE id = " . $item['id'] . "");

			$info = explode("|", $object['inf']);

			$this->game->addToLog($this->user->id, 'снял', $info[1], 'рынок');

			$message = "Предмет <u>" . $info[1] . "</u> снять с продажи";
		}
		else
			$message = "Предмет не найден в Вашем рюкзаке!";
	}
	else
		$message = "Предмет не найден на рынке!";
}

if ($this->request->hasQuery('sale'))
{
	$sale = (int) $this->request->getQuery('sale', 'int');
	$price = (float) str_replace(',', '.', $this->request->getPost('credits'));

	$object = $this->db->query("SELECT id, inf, tip FROM game_objects WHERE id = " . $sale . " AND user_id = ".$this->user->id." AND present = 0 AND onset = 0")->fetch();

	if (isset($object['id']))
	{
		$info = explode("|", $object['inf']);

		if ($object['tip'] != 12)
		{
			if ($info[5] != 1)
			{
				$sale_price = round($info[2] * 0.62, 2);

				if ($price >= $sale_price)
				{
					$success = $this->db->query("UPDATE game_objects SET komis = '1' WHERE id = " . $object['id'] . "");

					if ($success)
					{
						$this->db->query("INSERT INTO game_market (`group_id`, `object_id`, `user_id`, `price`, time) values('" . $object['tip'] . "', '" . $object['id'] . "', '" . $this->user->id . "', '".$price."', ".time().")");

						$this->game->addToLog($this->user->id, 'сдал', $info[1] . " (" . $price . " кр)", 'рынок');

						$message = "Предмет <u>" . $info[1] . "</u> взят на продажу";
					}
					else
						$message = 'Произошла ошибка';
				}
				else
					$message = "Вы не можете сдать на коммисию предмет по такой цене!";
			}
			else
				$message = "Предмет <u>" . $info[1] . "</u> не подледжит продаже!";
		}
		else
			$message = "Предмет <u>" . $info[1] . "</u> не подледжит продаже!";
	}
	else
		$message = "Предмет не найден в Вашем рюкзаке!";
}

if ($this->request->hasQuery('buy'))
{
	$buy = (int) $this->request->getQuery('buy', 'int');

	$item = $this->db->query("SELECT * FROM game_market WHERE id = ".$buy." AND user_id != ".$this->user->id."")->fetch();

	if (isset($item['id']))
	{
		$object = $this->db->query("SELECT id, inf FROM game_objects WHERE id = " . $item['object_id'] . "")->fetch();

		if (isset($object['id']))
		{
			$info = explode("|", $object['inf']);

			if ($item['price'] <= $this->user->credits)
			{
				$this->db->query("UPDATE game_users SET credits = credits - " . $item['price'] . " WHERE id = '" . $this->user->id . "'");
				$this->db->query("UPDATE game_users SET credits = credits + " . $item['price'] . " WHERE id = '" . $item['user_id'] . "'");
				$this->db->query("UPDATE game_objects SET user_id = '" . $this->user->id . "', komis = '0' WHERE id = '" . $object['id'] . "'");
				$this->db->query("DELETE FROM game_market WHERE id = '" . $item['id'] . "'");

				$this->game->addToLog($this->user->id, 'купил', $info[1] . " (" . $item['price'] . " кр)", 'рынок');
				$this->game->addToLog($item['user_id'], 'продал', $info[1] . " (" . $item['price'] . " кр)", 'рынок');

				$user = $this->db->query("SELECT id, username FROM game_users WHERE id = ".$item['user_id']."")->fetch();

				$this->game->insertInChat("Ваш предмет <b>" . $info['1'] . "</b> куплен на рынке игроком <b>".$this->user->username."</b> за " . $item['price'] . " кр", $user['username'], true);

				$message = "Вы купили предмет за <u>" . $item['price'] . "</u> зол.";
			}
			else
				$message = "У Вас недостаточно денег для покупки предмета <u>" . $info['1'] . "</u>";
		}
		else
			$message = "Предмет не найден!";
	}
	else
		$message = "Предмет не найден в магазине!";
}

if ($otdel == 0)
{
	$builder = $this->modelsManager->createBuilder();

	$objects =  $builder->columns(Array('object.*', 'market.*', 'user.username'))
						->from(['object' => 'App\Models\Objects', 'market' => 'App\Models\Market', 'user' => 'App\Models\Users'])
						->where('user.id = market.user_id AND object.id = market.object_id AND object.komis = 1')
						->orderBy('market.time DESC')
						->limit(10)
						->getQuery()->execute();
}
elseif ($otdel < 40)
{
	$builder = $this->modelsManager->createBuilder();

	$objects =  $builder->columns(Array('object.*', 'market.*', 'user.username'))
						->from(['object' => 'App\Models\Objects', 'market' => 'App\Models\Market', 'user' => 'App\Models\Users'])
						->where('user.id = market.user_id AND object.id = market.object_id AND object.komis = 1 AND market.group_id = :group:', Array('group' => $otdel))
						->orderBy('market.price ASC')
						->getQuery()->execute();
}
else
{
	$objects = $this->user->getSlot()->getInventoryObjects(0, 'tip NOT IN (12,13,15,16,17,21,22) AND onset = 0');
}

$this->view->pick('shared/city/1_komis');

$this->view->setVar('otdel', $otdel);
$this->view->setVar('objects', $objects);
$this->view->setVar('message', $message);

?>