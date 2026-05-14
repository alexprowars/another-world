<?php

namespace App\Models;

use App\Exceptions\Exception;
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
		if ($this->count <= 0) {
			throw new Exception("Предмета нет на складе");
		}

		if (!$this->item) {
			throw new Exception("Предмет не найден!");
		}

		$user = auth()->user();

		if ($user->tutorial == 3 && $this->item->id == 817) {
			$this->item->price = 0;

			$user->tutorial++;
			$user->update();
		}

		if ($this->item->credits > 0) {
			$price = $this->item->credits;

			if ($user->vip == 1) {
				$price = $this->item->getVipPrice();
			}

			if ($price > $user->credits) {
				throw new Exception("У Вас недостаточно денег для покупки предмета <u>" . $this->item->title . "</u>");
			}

			$user->credits -= $price;
		} else {
			$price = $this->item->price;

			if ($user->vip == 1) {
				$price = $this->item->getVipPrice();
			}

			if ($user->profession == 8) {
				$price = $this->item->getMerchantPrice();
			}

			if ($price > $user->moneys) {
				throw new Exception("У Вас недостаточно денег для покупки предмета <u>" . $this->item->title . "</u>");
			}

			$user->moneys -= $price;
		}

		$user->save();

		$this->setAttribute('count', $this->count - 1);
		$this->save();

		$this->item->addInInventory($user->id);



		//$game = $this->getDI()->getShared('game');
		//$game->addToLog($user->id, 'купил', $this->item->title . ' (' . $price . ' ' . ($this->item->f_price > 0 ? 'пл.' : 'зол.') . ')', 'гос магазин');

	}
}
