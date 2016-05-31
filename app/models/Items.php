<?php
namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * Class Objects
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 */

class Items extends Model
{
	public $id;
	public $name;
	public $title;
	public $price;
	public $f_price;
	public $tip;
	public $slot1;
	public $slot2;

	public $min_level;
	public $min_strength;
	public $min_dex;
	public $min_agility;
	public $min_vitality;
	public $min_razum;
	public $min_proff;

	public $strength;
	public $dex;
	public $agility;
	public $vitality;
	public $razum;

	public $min;
	public $max;

	public $br1;
	public $br2;
	public $br3;
	public $br4;
	public $br5;

	public $krit;
	public $mkrit;
	public $unkrit;
	public $uv;
	public $unuv;
	public $pblock;

	public $hp;
	public $energy;

	public $life;
	public $about;

	public function getSource()
	{
	 	return "game_items";
	}

	public function onConstruct()
	{
		$this->hasMany("id", 'App\Models\ShopItems', "item_id", Array('alias' => 'ShopItems'));
	}

	public function getMinDemands ()
	{
		$result = '';

		$user = $this->getDI()->getShared('user');

		if ($this->min_level > 0)
		{
			if ($user->level < $this->min_level)
				$result .= "<font color=red>Уровень: " . $this->min_level . "</font><br>";
			else
				$result .= "Уровень: " . $this->min_level. "<br>";
		}

		foreach (_getText('stats') as $code => $name)
		{
			if (!isset($this->{"min_".$code}))
				continue;

			if ($this->{"min_".$code} > 0)
			{
				if ($user->{$code} < $this->{"min_".$code})
					$result .= "<font color=red>".$name.": " . $this->{"min_".$code} . "</font><br>";
				else
					$result .= $name.": " . $this->{"min_".$code}. "<br>";
			}
		}

		// Проверка професии
		if ($this->min_proff > 0)
		{
			if ($user->proff != $this->min_proff)
				$result .= "<font color=red>Профессия: " . _getText('proffessions', $this->min_proff) . "</font><br>";
			else
				$result .= "Профессия: " . _getText('proffessions', $this->min_proff) . "<br>";
		}

		return $result;
	}

	public function getBounus ()
	{
		$result = '';

		if ($this->min > 0)
			$result .= "Минимальный урон: ".$this->formatStat($this->min)."<br>";
		if ($this->max > 0)
			$result .= "Максимальный урон: ".$this->formatStat($this->max)."<br>";

		if ($this->br1 > 0)
			$result .= "Броня головы: ".$this->formatStat($this->br1)."<br>";
		if ($this->br2 > 0)
			$result .= "Броня груди: ".$this->formatStat($this->br2)."<br>";
		if ($this->br3 > 0)
			$result .= "Броня живота: ".$this->formatStat($this->br3)."<br>";
		if ($this->br4 > 0)
			$result .= "Броня пояса: ".$this->formatStat($this->br4)."<br>";
		if ($this->br5 > 0)
			$result .= "Броня ног: ".$this->formatStat($this->br5)."<br>";

		foreach (_getText('stats') as $code => $name)
		{
			if (!isset($this->{$code}))
				continue;

			if ($this->{$code} != 0)
				$result .= $name.": ".$this->formatStat($this->{$code})."<br>";
		}

		if ($this->krit > 0)
			$result .= "Критического удара: ".$this->formatStat($this->krit)."%<br>";
		if ($this->mkrit > 0)
			$result .= "Мощность крит. удара: ".$this->formatStat($this->mkrit)."%<br>";
		if ($this->unkrit > 0)
			$result .= "Против критического удара: ".$this->formatStat($this->unkrit)."%<br>";
		if ($this->uv > 0)
			$result .= "Увёртливости: ".$this->formatStat($this->uv)."%<br>";
		if ($this->unuv > 0)
			$result .= "Против увёртливости: ".$this->formatStat($this->unuv)."%<br>";
		if ($this->pblock > 0)
			$result .= "Пробивание блока: ".$this->formatStat($this->pblock)."%<br>";

		if ($this->hp > 0)
			$result .= "Уровень жизни: ".$this->formatStat($this->hp)."<br>";
		if ($this->energy > 0)
			$result .= "Уровень маны: ".$this->formatStat($this->energy)."<br>";

		return $result;
	}

	public function formatStat ($value)
	{
		return ($value > 0 ? '+'.$value : '');
	}

	public function isSecondHand ()
	{
		return ($this->tip == 17 && $this->slot2 == "w5");
	}

	public function getVipPrice ()
	{
		if ($this->f_price > 0)
			return round($this->f_price * 0.67, 2);
		else
			return round($this->price * 0.85, 2);
	}

	public function addInInventory ($userId)
	{
		$inf = $this->name . "|" . $this->title . "|" . $this->price . "|0|" . (int) $this->isSecondHand() . "|" . $this->art . "|0|" . $this->iznos;
		$min = $this->min_level . "|" . $this->min_strength . "|" . $this->min_dex . "|" . $this->min_agility . "|" . $this->min_vitality . "|" . $this->min_razum . "|0|" . $this->min_proff;

		return $this->getDI()->getShared('db')->insertAsDict(
		   	"game_objects",
			array
		   	(
				'user_id' 	=> $userId,
				'inf' 		=> $inf,
				'min' 		=> $min,
				'tip' 		=> $this->tip,
				'br1'		=> $this->br1,
				'br2'		=> $this->br2,
				'br3'		=> $this->br3,
				'br4'		=> $this->br4,
				'br5'		=> $this->br5,
				'min_d'		=> $this->min,
				'max_d'		=> $this->max,
				'hp'		=> $this->hp,
				'energy'	=> $this->energy,
				'strength'	=> $this->strength,
				'dex'		=> $this->dex,
				'agility'	=> $this->agility,
				'vitality'	=> $this->vitality,
				'razum'		=> $this->razum,
				'krit'		=> $this->krit,
				'mkrit'		=> $this->mkrit,
				'unkrit'	=> $this->unkrit,
				'uv'		=> $this->uv,
				'unuv'		=> $this->unuv,
				'pblock'	=> $this->pblock,
				'mblock'	=> $this->mblock,
				'pbr'		=> $this->pbr,
				'time'		=> time(),
				'about'		=> $this->about,
				'class'		=> $this->class,
				'otravl'	=> $this->otravl,
				'use_mana'	=> $this->use_mana,
				'magic'		=> $this->magic,
				'life'		=> $this->life > 0 ? (time() + $this->life) : 0
		   	)
		);
	}
}

?>