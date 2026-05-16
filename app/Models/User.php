<?php

namespace App\Models;

use App\Facades\Vars;
use App\Http\Resources\UserSlotItemResource;
use App\Services\UserService;
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

	private $auraInfo = array();
	public $effects = 0;

	private $isEdit = false;

	public $tutorial;
	public $sign;
	public $obraz;
	public $provin;
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
	public $dexterity;
	public $agility;
	public $vitality;
	public $magic;
	public $intelligence;
	public $battery;

	/**
	 * Вычисляемые модификаторы
	 */
	public $armor1 = 0;
	public $armor2 = 0;
	public $armor3 = 0;
	public $armor4 = 0;
	public $armor5 = 0;

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
		'hp_now' => 'float',
		'blocked_at' => 'immutable_datetime',
		'online' => 'immutable_datetime',
		'r_date' => 'immutable_datetime',
		'silence' => 'immutable_datetime',
		'invisible' => 'immutable_datetime',
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
		return $this->hasOne(UserSlot::class, 'user_id')->chaperone();
	}

	/** @return BelongsTo<Tribe, $this> */
	public function tribe(): BelongsTo
	{
		return $this->belongsTo(Tribe::class, 'user_id');
	}

	/** @return BelongsTo<Battle, $this> */
	public function battle(): BelongsTo
	{
		return $this->belongsTo(Battle::class, 'battle_id');
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
		return $this->hasRole('admin');
	}

	public function isFree(): bool
	{
		return !$this->r_time;
	}

	public function isOnline(): bool
	{
		return $this->online?->diffInSeconds() < 180;
	}

	public function isBot(): bool
	{
		return $this->rank == 60;
	}

	public function calculate()
	{
		UserService::calculateWearsStats($this);
		UserService::calculateStats($this);
	}

	public function getSlot()
	{
		if (!$this->slots) {
			$this->slots()->make()->save();
		}

		return $this->slots;
	}

	public function getSlotsInfo()
	{
		$result = [];

		// Выбираем все вещи которые одеты на игроке
		$wears = $this->getSlot()->getItems();

		foreach ($wears as $object) {
			// В какой слот одета вещь
			$i = $object->onset;

			if ($i == 16 && empty($result['slot_' . $i])) {
				$i = 4;
			}

			$object->setPosition($i);

			$result['slot_' . $i] = new UserSlotItemResource($object);
		}

		return $result;
	}

	public function renderUserStatus()
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

	public function getAvatar(): string
	{
		if ($this->obraz) {
			return '/assets/images/avatar/obraz/' . $this->obraz . '.png';
		}

		return '/assets/images/avatar/1/' . ($this->gender === 'F' ? '2' : '1') . '.png';
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
