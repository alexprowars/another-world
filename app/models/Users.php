<?php
namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * Class Users
 * @package App\Models
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 */
class Users extends Model
{
	public 	$data 		= array();
	private $options 	= array('security' => 0);

	private $auraInfo = array();
	public 	$effects = 0;

	private $isEdit = false;

	/**
	 *
	 * @var integer
	 */
	public $id;
	public $tutorial;
	public $username;
	public $authlevel;
	public $onlinetime;
	public $silence;
	public $battle;
	public $r_type;
	public $r_time;
	public $room;
	public $level;
	public $up;
	public $exp;
	public $last_ip;
	public $rank;
	public $tribe;
	public $sign;
	public $vip;
	public $obraz;
	public $sex;
	public $wins;
	public $losses;
	public $drawn;
	public $provin;
	public $reit;
	public $proff;
	public $credits;
	public $f_credits;
	public $ip;
	public $s_updates;
	public $o_updates;
	public $item_type;
	public $b_tribe;
	public $tribe_rank;

	public $active;

	public $invisible;
	public $travma;
	public $ma_time;
	public $m_time;
	public $ch_time;
	public $f_time;
	public $t_time;
	public $immun;
	public $t_level;

	public $hp = 0;
	public $hp_now;
	public $hp_max = 0;
	public $energy = 0;
	public $energy_now;
	public $energy_max = 0;
	public $ustal_now;
	public $ustal_max = 0;

	public $strength;
	public $dex;
	public $agility;
	public $vitality;
	public $power;
	public $razum;
	public $battery;

	/**
	 * Вычисляемые модификаторы
	 */
	public $rating = 0;

	public $br1 = 0;
	public $br2 = 0;
	public $br3 = 0;
	public $br4 = 0;
	public $br5 = 0;

	public $min = 0;
	public $max = 0;

	public $krit 	= 0;
	public $unkrit 	= 0;
	public $uv 		= 0;
	public $unuv 	= 0;

	public $mblock 	= 0;
	public $pbr 		= 0;
	public $kbr 		= 0;
	public $pblock 	= 0;
	public $mkrit 	= 0;

	private $db;
	private $slots;

	public function onConstruct()
	{
		$this->db = $this->getDi()->getShared('db');

		if ($this->getDi()->getShared('router')->getControllerName() == 'edit')
			$this->isEdit = true;

		$this->hasOne('id', 'App\Models\Slots', 'user_id', Array('alias' => 'slot'));
	}

	public function isAdmin()
	{
		if ($this->id > 0)
			return ($this->authlevel == 3);
		else
			return false;
	}

	public function getId()
	{
		return $this->id;
	}

    public function getSource()
    {
        return "game_users";
    }

	public function unpackOptions ($opt, $isToggle = true)
	{
		$result = array();

		if ($isToggle)
		{
			$o = array_reverse(str_split(decbin($opt)));

			$i = 0;

			foreach ($this->options as $k => $v)
			{
				$result[$k] = (isset($o[$i]) ? $o[$i] : 0);

				$i++;
			}
		}

		return $result;
	}

	public function packOptions ($opt, $isToggle = true)
	{
		if ($isToggle)
		{
			$r = array();

			foreach ($this->options as $k => $v)
			{
				if (isset($opt[$k]))
					$v = $opt[$k];

				$r[] = $v;
			}

			return bindec(implode('', array_reverse($r)));
		}
		else
			return 0;
	}

	public function isFree ()
	{
		return ($this->r_time == 0 && $this->r_type == 0);
	}

	public function isOnline ()
	{
		return (time() - $this->onlinetime < 180);
	}

	public function isBot ()
	{
		return $this->rank == 60;
	}

	public function checkRoom ($room)
	{
		if ($room != $this->room)
		{
			$this->db->query("UPDATE person SET room = '".intval($room)."' WHERE id = '".$this->id."'");
			$this->room = $room;
		}
	}

	public function getPercent ($current, $max)
	{
		$result = ($current / $max) * 100;
		$result = min(100, max(0, $result));

		return $result;
	}

	public function checkEffects ()
	{
		if ($this->provin == 1)
			$this->battery = 1;

		//$user['hp'] = 0;
		//$user['energy'] = 0;

		if ($this->isEdit)
		{
			// МФ
			$this->krit 	+= ($this->dex * 5);
			$this->unkrit 	+= ($this->dex * 5);
			$this->uv 		+= ($this->agility * 5);
			$this->unuv 	+= ($this->agility * 5);
		}

		// Положительные и отрицательные эффекты на персонаже (элики, ауры, проклятья)
		$effects = $this->db->query("SELECT * FROM `game_users_effects` WHERE `user_id` = '" . $this->getId() . "' AND `time` > ".time()."");

		while ($effect = $effects->fetch())
		{
			$this->auraInfo[] = $effect;

			$this->strength += $effect['strength'];
			$this->dex 		+= $effect['dex'];
			$this->agility 	+= $effect['agility'];
			$this->vitality += $effect['vitality'];
			$this->power 	+= $effect['power'];
			$this->razum 	+= $effect['razum'];
			$this->battery 	+= $effect['battery'];
			$this->br1 		+= $effect['br1'];
			$this->br2 		+= $effect['br2'];
			$this->br3 		+= $effect['br3'];
			$this->br4 		+= $effect['br4'];
			$this->br5 		+= $effect['br5'];
			$this->min 		+= $effect['min'];
			$this->max 		+= $effect['max'];

			$this->effects++;
		}

		// Конец эффектов

		if ($this->strength < 0)
			$this->strength = 0;
		if ($this->dex < 0)
			$this->dex = 0;
		if ($this->agility < 0)
			$this->agility = 0;
		if ($this->vitality < 0)
			$this->vitality = 0;
		if ($this->power < 0)
			$this->power = 0;
		if ($this->razum < 0)
			$this->razum = 0;
		if ($this->battery < 0)
			$this->battery = 0;

		// HP, Energy, Battery

		$hp_max = $this->vitality * 5 + $this->hp;
		$this->energy_max = ceil($this->power * 5 + $this->energy);
		$this->ustal_max = $this->battery * 20;

		if ($this->hp_max != $hp_max)
		{
			$this->db->query("UPDATE `game_users` SET `hp_max` = '" . $hp_max . "' WHERE `id` = '" . $this->getId() . "'");
			$this->hp_max = $hp_max;
		}

		if ($this->hp_now > $this->hp_max)
		{
			$this->db->query("UPDATE `game_users` SET `hp_now` = '" . $this->hp_max . "' WHERE `id` = '" . $this->getId() . "'");
			$this->hp_now = $this->hp_max;
		}

		if ($this->energy_now > $this->energy_max)
		{
			$this->db->query("UPDATE `game_users` SET `energy_now` = '" . $this->energy_max . "' WHERE `id` = '" . $this->getId() . "'");
			$this->energy_now = $this->energy_max;
		}

		if ($this->ustal_now > $this->ustal_max)
		{
			$this->db->query("UPDATE `game_users` SET `ustal_now` = '" . $this->ustal_max . "' WHERE `id` = '" . $this->getId() . "'");
			$this->ustal_now = $this->ustal_max;
		}
	}

	/**
	 * @param mixed $parameters
	 * @return \App\Models\Slots
	 */
	public function getSlot($parameters = null)
	{
		if (!is_object($this->slots))
		{
			$result = $this->getRelated('slot', $parameters);

			if ($result === false)
			{
				$result = new Slots;
				$result->user_id = $this->id;
				$result->save();
			}

			$this->slots = $result;

			return $result;
		}
		else
			return $this->slots;
	}

	public function calculateParams ()
	{
		$slot = $this->getSlot();

		$wears = $slot->getWearsObjects();

		foreach ($wears as $object)
		{
			$this->strength 	+= $object->strength;
			$this->agility 		+= $object->agility;
			$this->dex 			+= $object->dex;
			$this->vitality 	+= $object->vitality;
			$this->razum 		+= $object->razum;

			$this->hp 			+= $object->hp;
			$this->energy 		+= $object->energy;

			$this->br1 		+= $object->br1;
			$this->br2 		+= $object->br2;
			$this->br3 		+= $object->br3;
			$this->br4 		+= $object->br4;
			$this->br5 		+= $object->br5;

			$this->krit 	+= $object->krit;
			$this->mkrit 	+= $object->mkrit;
			$this->unkrit 	+= $object->unkrit;
			$this->uv 		+= $object->uv;
			$this->unuv 	+= $object->unuv;

			$this->pblock 	+= $object->pblock;
			$this->mblock 	+= $object->mblock;
			$this->pbr 		+= $object->pbr;
			$this->kbr 		+= $object->kbr;

			$this->min 		+= $object->min_d;
			$this->max 		+= $object->max_d;
		}
	}

	function getSlotsInfo ()
	{
		/**
		 * @var Objects[] $result
		 */
		$result = array();

		// Выбираем все вещи которые одеты на игроке
		$slot = $this->getSlot();

		$wears = $slot->getWearsObjects();

		foreach ($wears as $object)
		{
			if ($object->life > 0 && $object->life < time())
			{
				$slot->unsetObject($object->onset);

				continue;
			}

			// В какой слот одета вещь
			$i = $object->onset;

			if ($i == 16 && !isset($result['slot_' . $i]))
				$i = 4;

			$object->setPosition($i);

			$this->strength 	+= $object->strength;
			$this->agility 		+= $object->agility;
			$this->dex 			+= $object->dex;
			$this->vitality 	+= $object->vitality;
			$this->razum 		+= $object->razum;

			$this->hp 			+= $object->hp;
			$this->energy 		+= $object->energy;

			if ($this->isEdit)
			{
				$this->br1 		+= $object->br1;
				$this->br2 		+= $object->br2;
				$this->br3 		+= $object->br3;
				$this->br4 		+= $object->br4;
				$this->br5 		+= $object->br5;

				$this->krit 	+= $object->krit;
				$this->mkrit 	+= $object->mkrit;
				$this->unkrit 	+= $object->unkrit;
				$this->uv 		+= $object->uv;
				$this->unuv 	+= $object->unuv;

				$this->pblock 	+= $object->pblock;
				$this->mblock 	+= $object->mblock;
				$this->pbr 		+= $object->pbr;
				$this->kbr 		+= $object->kbr;

				$this->min 		+= $object->min_d;
				$this->max 		+= $object->max_d;
			}

			$info = $object->getInf();

			// Для вычисления рейтинга (стоимость вещи)
			if ($info[2] != 0)
				$this->rating += $info[2];

			if ($this->isEdit)
			{
				$info[1] = "Снять " . $info[1];
				$object->setInf($info);
			}

			$result['slot_' . $i] = $object;
		}

		// Для пустых слотов
		for ($i = 1; $i < 23; $i++)
		{
			if (!isset($result['slot_' . $i]))
			{
				$result['slot_' . $i] = new Objects;
				$result['slot_' . $i]->setPosition($i);
			}
		}

		return $result;
	}

	public function getUserRaiting ()
	{
		// Вычисление рейтинга крутизны (цена вещей, статы, процент побед)
		$a = $this->strength + $this->agility + $this->dex + $this->vitality + $this->razum + $this->battery + $this->power - 14;
		$b = round($this->wins / ($this->losses + $this->wins + 0.000001), 2);

		$result = round(((($this->rating / 1000) + ($a / 10)) * $b) + ($this->level / 2), 2);

		return $result;
	}

	public function getPersonBlock ($isEdit = false)
	{
		$parse = array();

		$up = $this->db->query("SELECT `lev1`.`up`, `lev2`.`exp` FROM `game_levels` `lev1`, `game_levels` `lev2` WHERE `lev1`.`up` = '" . $this->up . "' AND `lev1`.`level` = '" . $this->level . "' AND `lev2`.`id` = `lev1`.`id` + 1")->fetch();

		$parse['ups_up'] 	= $up['up'];
		$parse['ups_exp'] 	= $up['exp'];

		if (!$this->obraz)
			$parse['obraz'] = "1/" . $this->sex;

		$parse['var_edit'] = $isEdit ? 1 : 0;

		$user = $this->toArray();

		foreach ($user as $key => $value)
			$parse['~'.$key] = $value;

		$parse += $this->getSlotsInfo();
		$this->checkEffects();

		$parse += $this->toArray();

		$parse['hp_now'] 		= round($this->hp_now);
		$parse['energy_now'] 	= round($this->energy_now);

		$parse['hp'] 		= $this->hp;
		$parse['energy'] 	= $this->energy;
		$parse['br1'] 		= $this->br1;
		$parse['br2'] 		= $this->br2;
		$parse['br3'] 		= $this->br3;
		$parse['br4'] 		= $this->br4;
		$parse['br5'] 		= $this->br5;
		$parse['min'] 		= $this->min;
		$parse['max'] 		= $this->max;
		$parse['krit'] 		= $this->krit;
		$parse['unkrit'] 	= $this->unkrit;
		$parse['uv'] 		= $this->uv;
		$parse['unuv'] 		= $this->unuv;
		$parse['mblock'] 	= $this->mblock;
		$parse['pbr'] 		= $this->pbr;
		$parse['kbr'] 		= $this->kbr;
		$parse['pblock'] 	= $this->pblock;
		$parse['mkrit'] 	= $this->mkrit;

		$parse['hp_max'] 		= $this->hp_max;
		$parse['ustal_max'] 	= $this->ustal_max;
		$parse['energy_max'] 	= $this->energy_max;

		$parse['w_h'] = $this->getPercent($this->hp_now, $this->hp_max);
		$parse['w_e'] = $this->getPercent($this->energy_now, $this->energy_max);
		$parse['w_u'] = $this->getPercent($this->ustal_now, $this->ustal_max);

		$parse['sex'] = _getText('sex', $this->sex);

		$rait = $this->getUserRaiting();

		if ($this->reit != $rait)
			$this->db->query("UPDATE game_users SET reit = '" . $rait . "' WHERE id = '" . $this->getId() . "'");

		$parse['rating'] = $rait;

		$parse['proffession'] = _getText('proffessions', $this->proff);
		$parse['actions'] = $this->renderUserStatus();

		return $this->getDi()->getShared('view')->getPartial('shared/person', Array('parse' => $parse));
	}

	function renderUserStatus ()
	{
		$result = "";

		if ($this->m_time > time() || $this->sign > time() || $this->travma > time() || $this->invisible > time() || $this->immun > time() || $this->ma_time > time() || $this->ch_time > time() || $this->effects)
		{
			$result .= "<tr><td colspan='2'><hr /></td></tr>";

			// Молчанка
			if ($this->m_time > time())
				$result .= "<tr><td><a class=ch title='Запрещено общение в чате'><small>Чат:</small></a></td><td width=63><b><small>" . pretty_time($this->m_time) . "</small></b></td></tr>";

			// Ускорение
			if ($this->sign > time())
				$result .= "<tr><td><a class=ch title='На персонажа действует ускорение'><small>Ускорение:</small></a></td><td width=63><b><small>" . pretty_time($this->sign) . "</small></b></td></tr>";

			// Травма
			if ($this->travma > time())
				$result .= "<tr><td><a class=ch title='Персонаж травмирован'><small>Травма:</small></a></td><td width=63><b><small>" . pretty_time($this->travma) . "</small></b></td></tr>";

			// Грамота
			if ($this->invisible > time())
				$result .= "<tr><td><a class=ch title='Тень'><small>Тень:</small></a></td><td width=63><b><small>" . pretty_time($this->invisible) . "</small></b></td></tr>";

			//Защита от нападения
			if ($this->immun > time())
				$result .= "<tr><td><a class=ch title='Защита от нападения'><small>Защита:</small></a></td><td width=63><b><small>" . pretty_time($this->immun) . "</small></b></td></tr>";

			//Защита от нападения магией
			if ($this->ma_time > time())
				$result .= "<tr><td><a class=ch title='Защита от магии'><small>Защита:</small></a></td><td width=63><b><small>" . pretty_time($this->ma_time) . "</small></b></td></tr>";

			//Защита от вампиров
			if ($this->ch_time > time())
				$result .= "<tr><td><a class=ch title='Защита от вампиров'><small>Защита:</small></a></td><td width=63><b><small>" . pretty_time($this->ch_time) . "</small></b></td></tr>";

			foreach ($this->auraInfo as $aura)
			{
				$type = '';

				switch ($aura['type'])
				{
					case 1:
						$type = "Аура";
						break;
					case 2:
						$type = "Зелье";
						break;
					case 3:
						$type = "Травма";
						break;
				}

				if ($type != '')
					$result .= "<tr><td><small>" . $type . ":</small></td><td width=63><b><small>" . pretty_time($aura['time']) . "</small></b></td></tr>\n";
			}
		}

		return $result;
	}
}
 
?>