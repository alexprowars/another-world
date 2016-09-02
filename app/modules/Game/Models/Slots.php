<?php

namespace Game\Models;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Model;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * @method static Slots[]|Model\ResultsetInterface find(mixed $parameters = null)
 * @method static Slots findFirst(mixed $parameters = null)
 */
class Slots extends Model
{
	public $id;
	public $user_id;

	public $i1 = 0;
	public $i2 = 0;
	public $i3 = 0;
	public $i4 = 0;
	public $i5 = 0;
	public $i6 = 0;
	public $i7 = 0;
	public $i8 = 0;
	public $i9 = 0;
	public $i10 = 0;
	public $i11 = 0;
	public $i12 = 0;
	public $i13 = 0;
	public $i14 = 0;
	public $i15 = 0;
	public $i16 = 0;
	public $i17 = 0;
	public $i18 = 0;
	public $i19 = 0;
	public $i20 = 0;
	public $i21 = 0;
	public $i22 = 0;

	private $slots = 22;

	public function getSource()
	{
		return DB_PREFIX."slots";
	}

	public function onConstruct()
	{
		$this->useDynamicUpdate(true);
		$this->hasOne('user_id', 'Game\Models\User', 'id');
	}

	public function afterUpdate ()
	{
		$this->setSnapshotData($this->toArray());
	}

	/**
	 * @return \Game\Models\Objects[]
	 */
	public function getWearsObjects ()
	{
		$result = [];

		$items = $this->getItemsId();

		if (count($items) > 0)
			$result = Objects::query()->where('user_id = :user:')->bind(Array('user' => $this->user_id))->inWhere('id', $items)->execute();

		return $result;
	}

	public function getInventoryObjects ($type = 1, $filter = '')
	{
		$items = $this->getItemsId();

		switch ($type)
		{
			case 1:  $query = "((tip >= 1 AND tip <= 11) OR (tip >= 24 AND tip <= 25))"; break;
			case 2:  $query = "((tip >= 12 AND tip <= 13) OR tip >= 26)"; break;
			case 3:  $query = "tip = 14"; break;
			case 4:  $query = "tip >= 19 AND tip <= 20"; break;
			case 5:  $query = "tip = 21"; break;
			case 6:  $query = "tip >= 15 AND tip <= 18"; break;
			case 7:  $query = "tip = 22"; break;
			case 8:  $query = "tip = 23"; break;
			default: $query = ""; break;
		}

		$result = Objects::query()->where('user_id = :user: AND bank = 0 AND komis = 0 AND sclad = 0 '.($query != '' ? 'AND '.$query : '').' '.($filter != '' ? 'AND '.$filter : '').'')->bind(Array('user' => $this->user_id))->orderBy('time DESC');

		if (count($items))
			$result = $result->notInWhere('id', $items);

		return $result->execute();
	}

	public function getItemsId ()
	{
		$result = [];

		for ($i = 1; $i <= $this->slots; $i++)
			if ($this->{'i'.$i} > 0)
				$result[] = $this->{'i'.$i};

		$result = array_unique($result);

		return $result;
	}

	public function unsetObject ($slot = false)
	{
		$items = [];

		if ($slot === false)
		{
			$items = $this->getItemsId();

			for ($i = 1; $i <= $this->slots; $i++)
				$this->{'i'.$i} = 0;
		}
		elseif ($slot > 0 && isset($this->{'i'.$slot}))
		{
			$items[] = $this->{'i'.$slot};
			$this->{'i'.$slot} = 0;
		}

		if (count($items) && $this->update())
		{
			$this->getDI()->getShared('db')->query("UPDATE game_objects SET `onset` = '0' WHERE `onset` > '0' AND `user_id` = '".$this->user_id."' AND id IN (".implode(',', $items).")");
		}
	}

	public function onsetObject ($itemId, User $user)
	{
		$message = '';

		$object = Objects::findFirst('user_id = ' . $user->id . ' AND id = ' . intval($itemId) . '');

		if ($object !== false)
		{
			$info 	= $object->getInf();
			$min 	= $object->getMin();

			if (
				$user->level >= $min[0] && $info[6] < $info[7] &&
				$user->strength >= $min[1] && $user->dex >= $min[2] && $user->agility >= $min[3] && $user->vitality >= $min[4] && $user->razum >= $min[5] &&
				($min[7] == 0 || $user->proff == $min[7]) && !in_array($object->tip, Array(15, 16, 19, 20, 21, 22, 23)) && ($object->life == 0 || $object->life > time())
			)
			{
				$slot = 0;

				switch ($object->tip)
				{
					case 1:
						if ($this->i3 && $info[4])
							$slot = 5;
						else
							$slot = 3;
						break;
					case 2:
						$slot = 4;
						break;
					case 3:
						if (!$this->i6)
							$slot = 6;
						elseif (!$this->i7)
							$slot = 7;
						elseif (!$this->i8)
							$slot = 8;
						elseif (!$this->i10)
							$slot = 10;
						elseif (!$this->i11)
							$slot = 11;
						elseif (!$this->i12)
							$slot = 12;
						else
							$slot = 6;
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
						if (!$this->i17)
							$slot = 17;
						elseif (!$this->i18)
							$slot = 18;
						else
							$slot = 17;
						break;
					case 14:
						if (!$this->i17)
							$slot = 17;
						elseif (!$this->i18)
							$slot = 18;
						else
							$slot = 17;
						break;
					case 17:
						if ($this->i3 && $info[4])
							$slot = 5;
						else
							$slot = 3;
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

				if ($slot != 0)
				{
					$object->onset = $slot;
					$object->update();

					$this->{'i'.$slot} = $object->id;
					$this->update();
				}
			}
			else
				$message = 'Вы не можете надеть эту вещь';
		}
		else
			$message = 'Вещь не найдена';

		return $message;
	}
}