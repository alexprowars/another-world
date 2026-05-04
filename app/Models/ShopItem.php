<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopItem extends Model
{
	protected $table = 'shops_items';
	protected $guarded = [];

	/** @return BelongsTo<Shop, $this> */
	public function shop(): BelongsTo
	{
		return $this->belongsTo(Shop::class, 'shop_id');
	}

	/** @return BelongsTo<Item, $this> */
	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, 'item_id');
	}

	public function buy()
	{
		$result = '';

		$user = $this->getDI()->getShared('user');
		$db = $this->getDI()->getShared('db');

		if ($this->cnt > 0) {
			if (isset($this->item->id) && $this->item->id > 0) {
				if ($user->tutorial == 3 && $this->item->id == 817) {
					$this->item->price = 0;

					$user->tutorial++;
					$user->update();
				}

				if ($this->item->f_price > 0) {
					$price = (float) $this->item->f_price;

					if ($user->vip == 1) {
						$price = $this->item->getVipPrice();
					}

					$credits = $user->f_credits;
				} else {
					$price = (float) $this->item->price;

					if ($user->vip == 1) {
						$price = $this->item->getMerchantPrice();
					}
					if ($user->proff == 8) {
						$price = $this->item->getVipPrice();
					}

					$credits = $user->credits;
				}

				if ($price <= $credits) {
					$success = $db->query("UPDATE `game_shop_items` s, `game_users` u SET s.cnt = s.cnt - 1, u." . ($this->item->f_price > 0 ? 'f_' : '') . "credits = u." . ($this->item->f_price > 0 ? 'f_' : '') . "credits - " . $price . " WHERE s.id = '" . $this->id . "' AND u.id = '" . $user->id . "'");

					if ($success) {
						$r = $this->item->addInInventory($user->id);

						if ($r) {
							$result = "Вы купили предмет <u>" . $this->item->title . "</u> за <u>" . $this->item->price . "</u> " . ($this->item->f_price > 0 ? 'пл.' : 'зол.');

							$game = $this->getDI()->getShared('game');
							$game->addToLog($user->id, 'купил', $this->item->title . ' (' . $price . ' ' . ($this->item->f_price > 0 ? 'пл.' : 'зол.') . ')', 'гос магазин');
						}
					}
				} else {
					$result = "У Вас недостаточно денег для покупки предмета <u>" . $this->item->title . "</u>";
				}
			} else {
				$result = "Предмет не найден!";
			}
		} else {
			$result = "Предмета нет на складе";
		}

		return $result;
	}
}
