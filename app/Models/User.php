<?php

namespace App\Models;

use App\Facades\Vars;
use App\Http\Resources\UserSlotItemResource;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
	use Notifiable;
	use SoftDeletes;
	use InteractsWithMedia;

	protected $calculated = false;
	public $hp = 0;
	public $energy = 0;

	// Вычисляемые игровые характеристики
	public $strength;
	public $dexterity;
	public $agility;
	public $vitality;
	public $magic;
	public $intelligence;

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
		'options' => 'array',
		'tribe_rank' => 'integer',
		'inquisitor_check_until' => 'immutable_datetime',
		'hp_now' => 'float',
		'blocked_at' => 'immutable_datetime',
		'injury' => 'immutable_datetime',
		'prison_until' => 'immutable_datetime',
		'online' => 'immutable_datetime',
		'r_date' => 'immutable_datetime',
		'silence' => 'immutable_datetime',
		'invisible' => 'immutable_datetime',
		'magic_protection_until' => 'immutable_datetime',
		'attack_protection_until' => 'immutable_datetime',
		'vampire_protection_until' => 'immutable_datetime',
		'is_clone' => 'boolean',
		'vip' => 'immutable_datetime',
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
		return $this->belongsTo(Tribe::class);
	}

	/** @return BelongsTo<Battle, $this> */
	public function battle(): BelongsTo
	{
		return $this->belongsTo(Battle::class, 'battle_id');
	}

	/** @return BelongsTo<Work, $this> */
	public function work(): BelongsTo
	{
		return $this->belongsTo(Work::class, 'work_id');
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

	/** @return HasMany<UserAbility, $this> */
	public function abilities(): HasMany
	{
		return $this->hasMany(UserAbility::class, 'user_id');
	}

	/** @return HasMany<UserGift, $this> */
	public function gifts(): HasMany
	{
		return $this->hasMany(UserGift::class, 'user_id');
	}

	/** @return HasMany<UserFriend, $this> */
	public function friends(): HasMany
	{
		return $this->hasMany(UserFriend::class, 'user_id');
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

	public function isAdmin(): bool
	{
		return $this->rank === 100;
	}

	public function isFree(): bool
	{
		return !$this->r_date;
	}

	public function isOnline(): bool
	{
		return $this->online?->diffInSeconds() < 180;
	}

	public function isBot(): bool
	{
		return $this->rank == 60;
	}

	public function calculate(bool $persist = true): void
	{
		if ($this->calculated) {
			return;
		}

		UserService::calculateWearsStats($this, $persist);
		UserService::calculateStats($this, $persist);

		$this->calculated = true;
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

			$object->position = $i;

			$result['slot_' . $i] = new UserSlotItemResource($object);
		}

		return $result;
	}

	public function getAvatar(): string
	{
		if ($this->image) {
			return '/assets/images/avatar/' . $this->image;
		}

		return '/assets/images/avatar/1/' . ($this->gender === 'F' ? '2' : '1') . '.png';
	}
}
