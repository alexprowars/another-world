<?php

namespace App\Models;

use App\Exceptions\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class UserSlot extends Model
{
	protected $table = 'users_slots';
	protected $guarded = [];

	public $timestamps = false;

	protected const int MAX_SLOTS = 22;

	/** @return BelongsTo<User, $this> */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return Collection<int, UserItem>
	 */
	public function getWearsItems(): Collection
	{
		$items = $this->getItemsId();

		if (!empty($items)) {
			return UserItem::query()
				->where('user_id', $this->user_id)
				->whereIn('id', $items)
				->get();
		}

		return collect();
	}

	public function getInventoryObjects($type = 1, $filter = '')
	{
		$items = $this->getItemsId();

		switch ($type) {
			case 1:
				$query = "((tip >= 1 AND tip <= 11) OR (tip >= 24 AND tip <= 25))";
				break;
			case 2:
				$query = "((tip >= 12 AND tip <= 13) OR tip >= 26)";
				break;
			case 3:
				$query = "tip = 14";
				break;
			case 4:
				$query = "tip >= 19 AND tip <= 20";
				break;
			case 5:
				$query = "tip = 21";
				break;
			case 6:
				$query = "tip >= 15 AND tip <= 18";
				break;
			case 7:
				$query = "tip = 22";
				break;
			case 8:
				$query = "tip = 23";
				break;
			default:
				$query = "";
				break;
		}

		$result = Objects::query()->where('user_id = :user: AND bank = 0 AND komis = 0 AND sclad = 0 ' . ($query != '' ? 'AND ' . $query : '') . ' ' . ($filter != '' ? 'AND ' . $filter : '') . '')->bind(array('user' => $this->user_id))->orderBy('time DESC');

		if (count($items)) {
			$result = $result->notInWhere('id', $items);
		}

		return $result->execute();
	}

	public function getItemsId(): array
	{
		$result = [];

		for ($i = 1; $i <= self::MAX_SLOTS; $i++) {
			if ($this->{'i' . $i} > 0) {
				$result[] = $this->{'i' . $i};
			}
		}

		return array_unique($result);
	}

	public function unsetObject(?int $slot = null)
	{
		$items = [];

		if ($slot === null) {
			$items = $this->getItemsId();

			for ($i = 1; $i <= $this->slots; $i++) {
				$this->{'i' . $i} = 0;
			}
		} elseif (isset($this->{'i' . $slot})) {
			$items[] = $this->{'i' . $slot};
			$this->{'i' . $slot} = 0;
		}

		if (!empty($items) && $this->save()) {
			UserItem::query()
				->where('user_id', $this->user_id)
				->whereIn('id', $items)
				->update([
					'onset' => null,
				]);
		}
	}

	public function onsetObject($itemId, User $user)
	{
		$object = UserItem::query()
			->whereBelongsTo($user)
			->whereKey($itemId)
			->first();

		if (!$object) {
			throw new Exception('Вещь не найдена');
		}

		$info 	= $object->getInf();
		$min 	= $object->getMin();

		if (
			$user->level >= $min[0] && $info[6] < $info[7] &&
			$user->strength >= $min[1] && $user->dex >= $min[2] && $user->agility >= $min[3] && $user->vitality >= $min[4] && $user->razum >= $min[5] &&
			($min[7] == 0 || $user->proff == $min[7]) && !in_array($object->tip, array(15, 16, 19, 20, 21, 22, 23)) && ($object->life == 0 || $object->life > time())
		) {
			$slot = null;

			switch ($object->tip) {
				case 1:
				case 17:
					if ($this->i3 && $info[4]) {
						$slot = 5;
					} else {
						$slot = 3;
					}
					break;
				case 2:
					$slot = 4;
					break;
				case 3:
					if (!$this->i6) {
						$slot = 6;
					} elseif (!$this->i7) {
						$slot = 7;
					} elseif (!$this->i8) {
						$slot = 8;
					} elseif (!$this->i10) {
						$slot = 10;
					} elseif (!$this->i11) {
						$slot = 11;
					} elseif (!$this->i12) {
						$slot = 12;
					} else {
						$slot = 6;
					}
					break;
				case 4:
					$slot = 2;
					break;
				case 5:
					$slot = 5;
					break;
				case 6:
					$slot = 13;
					break;
				case 7:
					$slot = 9;
					break;
				case 8:
					$slot = 1;
					break;
				case 9:
					$slot = 15;
					break;
				case 10:
					$slot = 14;
					break;
				case 11:
					$slot = 16;
					break;
				case 12:
				case 14:
					if (!$this->i17) {
						$slot = 17;
					} elseif (!$this->i18) {
						$slot = 18;
					} else {
						$slot = 17;
					}
					break;
				case 18:
					$slot = 3;
					break;
				case 24:
					$slot = 21;
					break;
				case 25:
					$slot = 22;
					break;
				case 26:
					$slot = 20;
					break;
			}

			if ($slot) {
				$object->onset = $slot;
				$object->update();

				$this->{'i' . $slot} = $object->id;
				$this->save();
			}
		} else {
			throw new Exception('Вы не можете надеть эту вещь');
		}
	}
}
