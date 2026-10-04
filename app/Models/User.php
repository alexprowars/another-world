<?php

namespace App\Models;

use App\Engine\CombatStats;
use App\Engine\Services\UserService;
use App\Engine\World\Location;
use App\Http\Resources\UserSlotItemResource;
use App\Notifications\ResetPasswordNotification;
use Carbon\CarbonImmutable;
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

	private ?CombatStats $combatStats = null;

	protected $casts = [
		'options' => 'array',
		'tribe_rank' => 'integer',
		'inquisitor_check' => 'immutable_datetime',
		'hp_now' => 'float',
		'blocked_at' => 'immutable_datetime',
		'injury' => 'immutable_datetime',
		'prison' => 'immutable_datetime',
		'online' => 'immutable_datetime',
		'r_date' => 'immutable_datetime',
		'silence' => 'immutable_datetime',
		'invisible' => 'immutable_datetime',
		'battle_fury' => 'immutable_datetime',
		'magic_protection' => 'immutable_datetime',
		'attack_protection' => 'immutable_datetime',
		'vampire_protection' => 'immutable_datetime',
		'is_clone' => 'boolean',
		'vip' => 'immutable_datetime',
	];

	protected static function booted(): void
	{
		self::created(function (self $user) {
			$user->slots()->create();
		});
	}

	/** @param string $token */
	public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
	{
		$this->notify(new ResetPasswordNotification($token));
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
		return $this->hasMany(Effect::class, 'user_id')
			->whereFuture('date')
			->orderBy('id');
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

	public function currentLocation(): Location
	{
		return Location::fromCode($this->location);
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

	public function calculate(bool $persist = true, ?CarbonImmutable $time = null): void
	{
		if ($this->calculated) {
			return;
		}

		$time ??= CarbonImmutable::now();

		UserService::calculateWearsStats($this, $time, $persist);
		UserService::calculateStats($this, $persist);

		$this->calculated = true;
	}

	public function getCombatStats(): CombatStats
	{
		return $this->combatStats ??= new CombatStats(
			strength: $this->strength ?? 0,
			dexterity: $this->dexterity ?? 0,
			agility: $this->agility ?? 0,
			vitality: $this->vitality ?? 0,
			magic: $this->magic ?? 0,
			intelligence: $this->intelligence ?? 0,
		);
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

		return '/assets/images/avatar/1/' . ($this->gender === 'F' ? '2' : '1') . '.jpg';
	}
}
