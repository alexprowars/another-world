<?php

namespace App\Models;

use App\Facades\Vars;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName, HasMedia
{
	use HasRoles;
	use Notifiable;
	use SoftDeletes;
	use InteractsWithMedia;

	public $data 		= array();
	private $options 	= array('security' => 0);

	private $auraInfo = array();
	public $effects = 0;

	private $isEdit = false;

	public $tutorial;
	public $silence;
	public $battle;
	public $r_type;
	public $r_time;
	public $rank;
	public $sign;
	public $vip;
	public $obraz;
	public $provin;
	public $reit;
	public $proff;
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
	public $energy = 0;

	// Вычисляемые игровые характеристики
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

	public $krit	= 0;
	public $unkrit	= 0;
	public $uv		= 0;
	public $unuv	= 0;

	public $mblock	= 0;
	public $pbr		= 0;
	public $kbr		= 0;
	public $pblock	= 0;
	public $mkrit	= 0;

	protected $casts = [
		'blocked_at' => 'immutable_datetime',
		'onlinetime' => 'immutable_datetime',
	];

	protected static function booted(): void
	{
		self::created(function (self $user) {
			$user->slots()->create();
		});

		self::retrieved(function (self $user) {
			foreach (Vars::getStats() as $stat) {
				$user->{$stat} = $user->{'s_' . $stat};
			}
		});
	}

	/** @return HasOne<UserSlot, $this> */
	public function slots(): HasOne
	{
		return $this->hasOne(UserSlot::class, 'user_id');
	}

	/** @return BelongsTo<Tribe, $this> */
	public function tribe(): BelongsTo
	{
		return $this->belongsTo(Tribe::class, 'user_id');
	}

	/** @return HasMany<UserItem, $this> */
	public function items(): HasMany
	{
		return $this->hasMany(UserItem::class, 'user_id');
	}

	/** @return HasMany<Effect, $this> */
	public function effects(): HasMany
	{
		return $this->hasMany(Effect::class, 'user_id');
	}

	/** @return HasMany<UserAuthentication, $this> */
	public function authentications(): HasMany
	{
		return $this->hasMany(UserAuthentication::class, 'user_id');
	}

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('default')
			->storeConversionsOnDisk('resize')
			->singleFile()
			->useDisk('media');
	}

	/** @return Attribute<string, string> */
	protected function ip(): Attribute
	{
		return Attribute::make(
			get: static fn($value) => long2ip($value),
			set: static fn($value) => sprintf("%u", ip2long($value)),
		);
	}

	public function isAdmin(): bool
	{
		if ($this->id > 0) {
			return $this->hasRole('admin');
		}

		return false;
	}

	public function onConstruct()
	{
		if ($this->getDI()->getShared('router')->getControllerName() == 'edit') {
			$this->isEdit = true;
		}
	}

	public function unpackOptions($opt, $isToggle = true)
	{
		$result = array();

		if ($isToggle) {
			$o = array_reverse(str_split(decbin($opt)));

			$i = 0;

			foreach ($this->options as $k => $v) {
				$result[$k] = (isset($o[$i]) ? $o[$i] : 0);

				$i++;
			}
		}

		return $result;
	}

	public function packOptions($opt, $isToggle = true)
	{
		if ($isToggle) {
			$r = array();

			foreach ($this->options as $k => $v) {
				if (isset($opt[$k])) {
					$v = $opt[$k];
				}

				$r[] = $v;
			}

			return bindec(implode('', array_reverse($r)));
		} else {
			return 0;
		}
	}

	public function isFree()
	{
		return ($this->r_time == 0 && $this->r_type == 0);
	}

	public function isOnline()
	{
		return (time() - $this->onlinetime < 180);
	}

	public function isBot()
	{
		return $this->rank == 60;
	}

	public function checkRoom($room)
	{
		if ($room != $this->room) {
			$this->room = $room;
			$this->update();
		}
	}

	public function getPercent($current, $max)
	{
		$result = ($current / $max) * 100;
		$result = min(100, max(0, $result));

		return $result;
	}

	public function calculate()
	{
		if ($this->provin == 1) {
			$this->battery = 1;
		}

		//$user['hp'] = 0;
		//$user['energy'] = 0;

		if ($this->isEdit) {
			// МФ
			$this->krit		+= ($this->dex * 5);
			$this->unkrit	+= ($this->dex * 5);
			$this->uv		+= ($this->agility * 5);
			$this->unuv		+= ($this->agility * 5);
		}

		// Положительные и отрицательные эффекты на персонаже (элики, ауры, проклятья)
		$effects = $this->effects()
			->whereFuture('date')
			->get();

		/** @var Effect $effect */
		foreach ($effects as $effect) {
			$this->auraInfo[] = $effect;

			foreach (Vars::getStats() as $stat) {
				if (isset($effect[$stat])) {
					$this->{$stat} += $effect[$stat];
				}
			}

			$this->br1 += $effect->br1 ?? 0;
			$this->br2 += $effect->br2 ?? 0;
			$this->br3 += $effect->br3 ?? 0;
			$this->br4 += $effect->br4 ?? 0;
			$this->br5 += $effect->br5 ?? 0;
			$this->min += $effect->min ?? 0;
			$this->max += $effect->max ?? 0;

			$this->effects++;
		}
		// Конец эффектов

		foreach (Vars::getStats() as $stat) {
			if ($this->{$stat} < 0) {
				$this->{$stat} = 0;
			}
		}

		// HP, Energy, Battery
		$this->hp_max = $this->vitality * 5 + $this->hp;
		$this->hp_now = min($this->hp_now, $this->hp_max);

		$this->energy_max = ceil($this->power * 5 + $this->energy);
		$this->energy_now = min($this->energy_now, $this->energy_max);

		$this->ustal_max = $this->battery * 20;
		$this->ustal_now = min($this->ustal_now, $this->ustal_max);

		$this->update();
	}

	public function getSlot()
	{
		if (!$this->slots) {
			$this->slots()->make()->save();

			return $this->slots;
		} else {
			return $this->slots;
		}
	}

	public function calculateParams()
	{
		$slot = $this->getSlot();

		$wears = $slot->getWearsObjects();

		foreach ($wears as $object) {
			foreach (Vars::getStats() as $stat) {
				if (isset($object->{$stat})) {
					$this->{$stat} += $object->{$stat};
				}
			}

			$this->hp			+= $object->hp;
			$this->energy		+= $object->energy;

			$this->br1		+= $object->br1;
			$this->br2		+= $object->br2;
			$this->br3		+= $object->br3;
			$this->br4		+= $object->br4;
			$this->br5		+= $object->br5;

			$this->krit		+= $object->krit;
			$this->mkrit	+= $object->mkrit;
			$this->unkrit	+= $object->unkrit;
			$this->uv		+= $object->uv;
			$this->unuv		+= $object->unuv;

			$this->pblock	+= $object->pblock;
			$this->mblock	+= $object->mblock;
			$this->pbr		+= $object->pbr;
			$this->kbr		+= $object->kbr;

			$this->min		+= $object->min_d;
			$this->max		+= $object->max_d;
		}
	}

	public function getSlotsInfo()
	{
		/**
		 * @var Objects[] $result
		 */
		$result = array();

		// Выбираем все вещи которые одеты на игроке
		$slot = $this->getSlot();

		$wears = $slot->getWearsItems();

		foreach ($wears as $object) {
			if ($object->life > 0 && $object->life < time()) {
				$slot->unsetObject($object->onset);

				continue;
			}

			// В какой слот одета вещь
			$i = $object->onset;

			if ($i == 16 && !isset($result['slot_' . $i])) {
				$i = 4;
			}

			$object->setPosition($i);

			foreach (Vars::getStats() as $stat) {
				if (isset($object->{$stat})) {
					$this->{$stat} += $object->{$stat};
				}
			}

			$this->hp			+= $object->hp;
			$this->energy		+= $object->energy;

			if ($this->isEdit) {
				$this->br1		+= $object->br1;
				$this->br2		+= $object->br2;
				$this->br3		+= $object->br3;
				$this->br4		+= $object->br4;
				$this->br5		+= $object->br5;

				$this->krit		+= $object->krit;
				$this->mkrit	+= $object->mkrit;
				$this->unkrit	+= $object->unkrit;
				$this->uv		+= $object->uv;
				$this->unuv		+= $object->unuv;

				$this->pblock	+= $object->pblock;
				$this->mblock	+= $object->mblock;
				$this->pbr		+= $object->pbr;
				$this->kbr		+= $object->kbr;

				$this->min		+= $object->min_d;
				$this->max		+= $object->max_d;
			}

			$info = $object->getInf();

			// Для вычисления рейтинга (стоимость вещи)
			if ($info[2] != 0) {
				$this->rating += $info[2];
			}

			if ($this->isEdit) {
				$info[1] = "Снять " . $info[1];
				$object->setInf($info);
			}

			$result['slot_' . $i] = $object;
		}

		// Для пустых слотов
		for ($i = 1; $i < 23; $i++) {
			if (!isset($result['slot_' . $i])) {
				$result['slot_' . $i] = new UserItem();
				$result['slot_' . $i]->setPosition($i);
			}
		}

		return $result;
	}

	public function getUserRaiting()
	{
		// Вычисление рейтинга крутизны (цена вещей, статы, процент побед)
		$a = $this->strength + $this->agility + $this->dex + $this->vitality + $this->razum + $this->battery + $this->power - 14;
		$b = round($this->wins / ($this->losses + $this->wins + 0.000001), 2);

		$result = round(((($this->rating / 1000) + ($a / 10)) * $b) + ($this->level / 2), 2);

		return $result;
	}

	public function getPersonBlock($isEdit = false)
	{
		$parse = array();

		$up = $this->db->query("SELECT `lev1`.`up`, `lev2`.`exp` FROM `game_levels` `lev1`, `game_levels` `lev2` WHERE `lev1`.`up` = '" . $this->up . "' AND `lev1`.`level` = '" . $this->level . "' AND `lev2`.`id` = `lev1`.`id` + 1")->fetch();

		$parse['ups_up'] 	= $up['up'];
		$parse['ups_exp'] 	= $up['exp'];

		if (!$this->obraz) {
			$parse['obraz'] = "1/" . $this->sex;
		}

		$parse['var_edit'] = $isEdit ? 1 : 0;

		$user = $this->toArray();

		foreach ($user as $key => $value) {
			$parse['~' . $key] = $value;
		}

		$parse += $this->getSlotsInfo();
		$this->calculate();

		$parse += $this->toArray();

		$parse['hp_now']		= round($this->hp_now);
		$parse['energy_now']	= round($this->energy_now);

		$parse['hp']		= $this->hp;
		$parse['energy']	= $this->energy;
		$parse['br1']		= $this->br1;
		$parse['br2']		= $this->br2;
		$parse['br3']		= $this->br3;
		$parse['br4']		= $this->br4;
		$parse['br5']		= $this->br5;
		$parse['min']		= $this->min;
		$parse['max']		= $this->max;
		$parse['krit']		= $this->krit;
		$parse['unkrit']	= $this->unkrit;
		$parse['uv']		= $this->uv;
		$parse['unuv']		= $this->unuv;
		$parse['mblock']	= $this->mblock;
		$parse['pbr']		= $this->pbr;
		$parse['kbr']		= $this->kbr;
		$parse['pblock']	= $this->pblock;
		$parse['mkrit']		= $this->mkrit;
		$parse['hp_max']	= $this->hp_max;
		$parse['ustal_max']	= $this->ustal_max;
		$parse['energy_max'] = $this->energy_max;

		$parse['w_h'] = $this->getPercent($this->hp_now, $this->hp_max);
		$parse['w_e'] = $this->getPercent($this->energy_now, $this->energy_max);
		$parse['w_u'] = $this->getPercent($this->ustal_now, $this->ustal_max);

		$parse['sex'] = _getText('sex', $this->sex);

		$rait = $this->getUserRaiting();

		if ($this->reit != $rait) {
			$this->reit = $rait;
			$this->update();
		}

		$parse['rating'] = $this->reit;

		$parse['proffession'] = _getText('proffessions', $this->proff);
		$parse['actions'] = $this->renderUserStatus();

		return $this->getDI()->getShared('view')->getPartial('shared/person', array('parse' => $parse));
	}

	function renderUserStatus()
	{
		$result = "";

		if ($this->m_time > time() || $this->sign > time() || $this->travma > time() || $this->invisible > time() || $this->immun > time() || $this->ma_time > time() || $this->ch_time > time() || $this->effects) {
			$result .= "<tr><td colspan='2'><hr /></td></tr>";

			// Молчанка
			if ($this->m_time > time()) {
				$result .= "<tr><td><a class=ch title='Запрещено общение в чате'><small>Чат:</small></a></td><td width=63><b><small>" . pretty_time($this->m_time) . "</small></b></td></tr>";
			}

			// Ускорение
			if ($this->sign > time()) {
				$result .= "<tr><td><a class=ch title='На персонажа действует ускорение'><small>Ускорение:</small></a></td><td width=63><b><small>" . pretty_time($this->sign) . "</small></b></td></tr>";
			}

			// Травма
			if ($this->travma > time()) {
				$result .= "<tr><td><a class=ch title='Персонаж травмирован'><small>Травма:</small></a></td><td width=63><b><small>" . pretty_time($this->travma) . "</small></b></td></tr>";
			}

			// Грамота
			if ($this->invisible > time()) {
				$result .= "<tr><td><a class=ch title='Тень'><small>Тень:</small></a></td><td width=63><b><small>" . pretty_time($this->invisible) . "</small></b></td></tr>";
			}

			//Защита от нападения
			if ($this->immun > time()) {
				$result .= "<tr><td><a class=ch title='Защита от нападения'><small>Защита:</small></a></td><td width=63><b><small>" . pretty_time($this->immun) . "</small></b></td></tr>";
			}

			//Защита от нападения магией
			if ($this->ma_time > time()) {
				$result .= "<tr><td><a class=ch title='Защита от магии'><small>Защита:</small></a></td><td width=63><b><small>" . pretty_time($this->ma_time) . "</small></b></td></tr>";
			}

			//Защита от вампиров
			if ($this->ch_time > time()) {
				$result .= "<tr><td><a class=ch title='Защита от вампиров'><small>Защита:</small></a></td><td width=63><b><small>" . pretty_time($this->ch_time) . "</small></b></td></tr>";
			}

			foreach ($this->auraInfo as $aura) {
				$type = '';

				switch ($aura['type']) {
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

				if ($type != '') {
					$result .= "<tr><td><small>" . $type . ":</small></td><td width=63><b><small>" . pretty_time($aura['time']) . "</small></b></td></tr>\n";
				}
			}
		}

		return $result;
	}

	public function saveData($fields, $userId = 0)
	{
		$this->db->updateAsDict($this->getSource(), $fields, ['conditions' => 'id = ?', 'bind' => array(($userId > 0 ? $userId : $this->id))]);
	}

	public function toArray($columns = null)
	{
		$result = parent::toArray($columns);

		foreach (Vars::getStats() as $stat) {
			$result[$stat] = $result['s_' . $stat];
		}

		return $result;
	}

	public function canAccessPanel(Panel $panel): bool
	{
		return $this->id === 1 || $this->can('panel');
	}

	public function getFilamentName(): string
	{
		return $this->nickname;
	}
}
