<?

/**
 * @var \App\Controllers\MapController $this
 */


use App\Models\ShopItems;

define('SHOP_ID', 4);

$otdel = $this->request->get('otdel', 'int', 0);

$message = '';


if ($this->request->hasPost('gift'))
{
	$who 	= (int) $this->request->getPost('who', 'int', 1);
	$user 	= addslashes($this->request->getPost('login'));
	$id 	= (int) $this->request->getPost('gift', 'int', 0);

	if ($who != 1 && $who != 2 && $who != 3)
		$who = 1;
	if (!$this->user->tribe && $who == 2)
		$who = 1;

	if (empty($user))
		$message = "Укажите логин персонажа, которому Вы хотите сделать подарок!";
	else
	{
		$info = $this->db->query("SELECT id, username FROM game_users WHERE username = '" . addslashes($user) . "'")->fetch();

		if (!isset($info['id']))
			$message = "Персонаж <u>".$user."</u> не найден!";
		elseif ($info['id'] == $this->user->id)
			$message = "Нельзя подарить что-либо самому себе!";
		elseif ($this->user->level < 2)
			$message = "Только начиная с 2 уровня Вы можете дарить подарки!";
		else
		{
			$object = $this->db->query("SELECT id, inf FROM game_objects WHERE id = " . $id . " AND user_id = '" . $this->user->id . "'")->fetch();

			if (isset($object['id']))
			{
				$ObjInfo = explode("|", $object['inf']);

				if ($this->db->query("SELECT id FROM game_users_prizes WHERE id = " . $id . "")->numRows())
					$message = "Этот предмет уже был подарен ранее!";
				elseif ($ObjInfo[5])
					$message = "Вы не можете дарить артефакты!";
				else
				{
					switch ($who)
					{
						case 1:
							$who = "user";
							break;
						case 2:
							$who = "tribe";
							break;
						case 3:
							$who = "anonim";
							break;
					}

					$text = htmlspecialchars(addslashes($this->request->getPost('text', null, '')));

					$this->db->insertAsDict(
						"game_users_prizes",
						array
						(
							'user_id' 	=> $info['id'],
							'tribe_id' 	=> $this->user->tribe,
							'who' 		=> $who,
							'object_id' => $object['id'],
							'sender_id' => $this->user->id,
							'text' 		=> $text
						)
					);

					$this->db->query("UPDATE game_objects SET user_id = '" . $info['id'] . "', present = 1 WHERE id = '" . $object['id'] . "'");

					$message = "Подарок передан к <u>" . $info['username'] . "</u>!";
				}
			}
		}
	}
}

if ($this->request->has('buy'))
{
	$buy = (int) $this->request->get('buy', 'int', 0);

	if ($buy > 0)
	{
		/**
		 * @var $item ShopItems
		 */
		$item = ShopItems::findFirst("shop_id = ".SHOP_ID." AND id = ".$buy."");
		$message = $item->buy();
	}
}

if ($otdel < 4)
{
	$builder = $this->modelsManager->createBuilder();

	$objects = $builder->from(['item' => 'App\Models\Items', 'shop' => 'App\Models\ShopItems'])->where('item.id = shop.item_id AND shop.shop_id = :shop: AND shop.group_id = :group:', Array('shop' => SHOP_ID, 'group' => $otdel))->orderBy('item.price ASC')->getQuery()->execute();
}
else
{
	$objects = $this->user->getSlot()->getInventoryObjects(0, "(inf LIKE ('flowers%') OR inf LIKE ('otkr%') OR tip = 15 OR tip = 16 OR tip = 17)");
}

$this->view->pick('shared/city/1_gshop');
$this->view->setVar('otdel', $otdel);
$this->view->setVar('objects', $objects);
$this->view->setVar('message', $message);

?>