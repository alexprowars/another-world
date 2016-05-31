<?php
namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * Class Objects
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 */
class Objects extends Model
{
	public $id;
	public $user_id;
	public $position = 0;
	public $inf;
	public $min;
	public $tip;
	public $onset;

	public $hp;
	public $energy;

	public $strength;
	public $dex;
	public $agility;
	public $vitality;
	public $power;
	public $razum;

	public $br1;
	public $br2;
	public $br3;
	public $br4;
	public $br5;

	public $min_d;
	public $max_d;

	public $krit;
	public $mkrit;
	public $unkrit;
	public $uv;
	public $unuv;

	public $pblock;
	public $mblock;
	public $pbr;
	public $kbr;

	public $time;
	public $life;
	public $about;

	public function getSource()
	{
	 	return "game_objects";
	}

	public function getInf ()
	{
		return explode("|", $this->inf);
	}

	public function getMin ()
	{
		return explode("|", $this->min);
	}

	public function setInf ($data)
	{
		if (is_array($data))
			$this->inf = implode('|', $data);
		else
			$this->inf = $data;
	}

	public function getPosition ()
	{
		return $this->position;
	}

	public function setPosition ($i)
	{
		$this->position = $i;
	}

	public function getSlotWidth ()
	{
		$result = 0;

		switch ($this->getPosition())
		{
			case 1: case 2: case 3: case 4: case 5:
				$result = 60;
			break;
			case 6: case 7: case 8:
				$result = 20;
			break;
			case 9:
				$result = 60;
			break;
			case 10: case 11: case 12:
				$result = 20;
			break;
			case 13: case 14: case 15:
			case 16:
				$result = 60;
			break;
			case 17: case 18: case 19:
				$result = 40;
			break;
			case 20:
				$result = 120;
			break;
			case 21: case 22:
				$result = 60;
			break;
		}

		return $result;
	}

	public function getSlotHeight ()
	{
		$result = 0;

		switch ($this->getPosition())
		{
			case 1: $result = 58; break;
			case 2: $result = 19; break;
			case 3: $result = 58; break;
			case 4: $result = 78; break;
			case 5: $result = 60; break;
			case 6: $result = 20; break;
			case 7: $result = 20; break;
			case 8: $result = 20; break;
			case 9: $result = 28; break;
			case 10: $result = 20; break;
			case 11: $result = 20; break;
			case 12: $result = 20; break;
			case 13: $result = 40; break;
			case 14: $result = 40; break;
			case 15: $result = 40; break;
			case 16: $result = 78; break;
			case 17: $result = 25; break;
			case 18: $result = 25; break;
			case 19: $result = 25; break;
			case 20: $result = 60; break;
			case 21: $result = 19; break;
			case 22: $result = 80; break;
		}

		return $result;
	}

	public function view ($isEdit = false)
	{
		if ($this->id)
			$info = $this->getInf();
		else
			$info = ['w'.$this->getPosition()];

		if ($this->id > 0)
			$hint = "it('" . $info[1] . "','" . $info[6] . " [" . $info[7] . "]','" . _getText('weapon', $this->tip) . "','" . $this->min_d . "','" . $this->max_d . "','" . $this->hp . "','" . $this->energy . "','" . $info[3] . "','".$this->getPosition()."');";
		else
			$hint = "it('','','','','','','','','".$this->getPosition()."','');";

		$html = '<img src="/images/items/'.$this->tip.'/' . $info[0] . '.gif" width="' . $this->getSlotWidth() . '" height="' . $this->getSlotHeight() . '" class="tooltip script '.($isEdit && $this->id ? 'hand' : '').'" data-content="' . $hint . '"';

		if ($this->id > 0 && $isEdit)
			$html .= ' onclick="load(\'/edit/?unset=' . $this->onset . '\')"';
		if (($this->getPosition() == 17 || $this->getPosition() == 18) && $this->id > 0)
			$html .= ' onclick="ShowForm(\'' . $info[1] . '\',\'/map/\',\'\',\'\',\'1\',\'' . $info[0] . '\',\'' . $this->id . '\',\'w' . $this->getPosition() . '\',\'\');"';

		$html .= '>';

		return $html;
	}

	public function getMinDemands ()
	{
		$result = '';

		$demands = $this->getMin();

		/**
		 * @var $user \App\Models\Users
		 */
		$user = $this->getDi()->getShared('user');

		// Проверка уровня
		if ($demands[0] > 0)
		{
			if ($user->level < $demands[0])
				$result .= "<font color=red>Уровень: " . $demands[0] . "</font><br>";
			else
				$result .= "Уровень: " . $demands[0] . "<br>";
		}

		// Проверка силы
		if ($demands[1] > 0)
		{
			if ($user->strength < $demands[1])
				$result .= "<font color=red>Сила: " . $demands[1] . "</font><br>";
			else
				$result .= "Сила: " . $demands[1] . "<br>";
		}

		// Проверка удачи
		if ($demands[2] > 0)
		{
			if ($user->dex < $demands[2])
				$result .= "<font color=red>Удача: " . $demands[2] . "</font><br>";
			else
				$result .= "Удача: " . $demands[2] . "<br>";
		}

		// Проверка проворства
		if ($demands[3] > 0)
		{
			if ($user->agility < $demands[3])
				$result .= "<font color=red>Ловкость: " . $demands[3] . "</font><br>";
			else
				$result .= "Ловкость: " . $demands[3] . "<br>";
		}

		// Проверка живучести
		if ($demands[4] > 0)
		{
			if ($user->vitality < $demands[4])
				$result .= "<font color=red>Выносливость: " . $demands[4] . "</font><br>";
			else
				$result .= "Выносливость: " . $demands[4] . "<br>";
		}

		// Проверка разума
		if ($demands[5] > 0)
		{
			if ($user->razum < $demands['5'])
				$result .= "<font color=red>Разум: " . $demands[5] . "</font><br>";
			else
				$result .= "Разум: " . $demands[5] . "<br>";
		}

		// Проверка професии
		if ($demands[7] > 0)
		{
			if ($user->proff != $demands[7])
				$result .= "<font color=red>Профессия: " . _getText('proffessions', $demands[7]) . "</font><br>";
			else
				$result .= "Профессия: " . _getText('proffessions', $demands[7]) . "<br>";
		}

		return $result;
	}

	public function getBonus ()
	{
		$result = '';

		if ($this->min_d > 0)
			$result .= "Минимальный урон: +" . $this->min_d . "<br>";
		if ($this->max_d > 0)
			$result .= "Максимальный урон: +" . $this->max_d . "<br>";

		if ($this->br1 > 0)
			$result .= "Броня головы: +" . $this->br1 . "<br>";
		if ($this->br2 > 0)
			$result .= "Броня груди: +" . $this->br2 . "<br>";
		if ($this->br3 > 0)
			$result .= "Броня живота: +" . $this->br3 . "<br>";
		if ($this->br4 > 0)
			$result .= "Броня пояса: +" . $this->br4 . "<br>";
		if ($this->br5 > 0)
			$result .= "Броня ног: +" . $this->br5 . "<br>";

		foreach (_getText('stats') as $code => $name)
		{
			if (!isset($this->{$code}))
				continue;

			if ($this->{$code} != 0)
				$result .= $name.": ".$this->formatStat($this->{$code})."<br>";
		}

		if ($this->krit > 0)
			$result .= "Критический удар: +" . $this->krit . "%<br>";
		if ($this->mkrit > 0)
			$result .= "Мощность крит. удара: +" . $this->mkrit . "%<br>";
		if ($this->unkrit > 0)
			$result .= "Против критического удара: +" . $this->unkrit . "%<br>";
		if ($this->uv > 0)
			$result .= "Увёртливость: +" . $this->uv . "%<br>";
		if ($this->unuv > 0)
			$result .= "Против увёртливости: +" . $this->unuv . "%<br>";
		if ($this->pblock > 0)
			$result .= "Пробивание блока: +" . $this->pblock . "%<br>";
		if ($this->mblock > 0)
			$result .= "Мощность блока: +" . $this->mblock . "%<br>";
		if ($this->pbr > 0)
			$result .= "Пробивание брони: +" . $this->pbr . "%<br>";
		if ($this->kbr > 0)
			$result .= "Крепкость брони: +" . $this->kbr . "%<br>";

		if ($this->hp > 0)
			$result .= "Жизнь: +" . $this->hp . "<br>";
		if ($this->energy > 0)
			$result .= "Мана: +" . $this->energy . "<br>";

		return $result;
	}

	public function formatStat ($value)
	{
		return ($value > 0 ? '+'.$value : '');
	}

	public function sale ()
	{
		/**
		 * @var $db \Phalcon\Db\Adapter\Pdo\Mysql
		 */
		$db = $this->getDi()->getShared('db');
		/**
		 * @var $user \App\Models\Users
		 */
		$user = $this->getDi()->getShared('user');

		$info = $this->getInf();

		if ($this->tip != 12 && $this->user_id == $user->id)
		{
			if (SHOP_ID == 2)
			{
				if ($info[5] != 1)
					$price = round($info[2]*0, 2);
				else
					$price = round($info[2]*0.5, 2);
			}
			else
			{
				if ($this->tip < 12)
					$price = round(($info[2] * (1 - ($info[6] / ($info[7] + 0.01)))) * 0.5, 2);
				else
					$price = round($info[2] * 0.5, 2);
			}

			if ($this->delete())
				$db->query("UPDATE game_users SET ".(SHOP_ID == 2 ? 'f_' : '')."credits = ".(SHOP_ID == 2 ? 'f_' : '')."credits + " . $price . " WHERE id = " . $user->id . "");

			$user->{(SHOP_ID == 2 ? 'f_' : '').'credits'} += $price;

			$result = "Вы удачно продали предмет <u>" . $info[1] . "</u> за <u>" . $price . "</u> зол.";

			$this->getDi()->getShared('game')->addToLog($user->id, 'продал', $info[1].' ('.$price.' зол.)', 'гос магазин');
		}
		else
			$result = "Предмет <u>" . $info[1] . "</u> не подледжит продаже!";

		return $result;
	}
}

?>