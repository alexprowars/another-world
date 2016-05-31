<?php
namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * Class Objects
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 */

class ShopItems extends Model
{
	public $id;
	public $shop_id;
	public $item_id;
	public $group_id;
	public $cnt;
	public $delivery;

	public function getSource()
	{
	 	return DB_PREFIX."shop_items";
	}

	public function onConstruct()
	{
	 	$this->belongsTo("shop_id", 'App\Models\Shop', "id", Array('alias' => 'shop'));
	 	$this->belongsTo("item_id", 'App\Models\Items', "id", Array('alias' => 'item'));
	}

	public function buy ()
	{
		$result = '';

		$user = $this->getDI()->getShared('user');
		$db = $this->getDI()->getShared('db');

		if ($this->cnt > 0)
		{
			if (isset($this->item->id) && $this->item->id > 0)
			{
				if ($user->tutorial == 3 && $this->item->id == 817)
				{
					$this->item->price = 0;

					$user->tutorial++;
					$db->query("UPDATE `game_users` SET `tutorial` = '".$user->tutorial."' WHERE `id` = '" . $user->getId() . "'");
				}

				if ($this->item->f_price > 0)
				{
					$price = (float) $this->item->f_price;

					if ($user->vip == 1)
						$price = $this->item->getVipPrice();

					$credits = $user->f_credits;
				}
				else
				{
					$price = (float) $this->item->price;

					if ($user->vip == 1)
						$price = $this->item->getMerchantPrice();
					if ($user->proff == 8)
						$price = $this->item->getVipPrice();

					$credits = $user->credits;
				}

				if ($price <= $credits)
				{
					$success = $db->query("UPDATE `game_shop_items` s, `game_users` u SET s.cnt = s.cnt - 1, u.".($this->item->f_price > 0 ? 'f_' : '')."credits = u.".($this->item->f_price > 0 ? 'f_' : '')."credits - " . $price . " WHERE s.id = '" . $this->id . "' AND u.id = '" . $user->id . "'");

					if ($success)
					{
						$r = $this->item->addInInventory($user->id);

						if ($r)
						{
							$result = "Вы купили предмет <u>" . $this->item->title . "</u> за <u>" . $this->item->price . "</u> ".($this->item->f_price > 0 ? 'пл.' : 'зол.');

							$game = $this->getDI()->getShared('game');
							$game->addToLog($user->id, 'купил', $this->item->title.' ('.$price.' '.($this->item->f_price > 0 ? 'пл.' : 'зол.').')', 'гос магазин');
						}
					}
				}
				else
					$result = "У Вас недостаточно денег для покупки предмета <u>" . $this->item->title . "</u>";
			}
			else
				$result = "Предмет не найден!";
		}
		else
			$result = "Предмета нет на складе";

		return $result;
	}
}

?>