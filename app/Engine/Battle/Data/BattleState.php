<?php

namespace App\Engine\Battle\Data;

use App\Engine\Battle\Abilities\Ability;
use App\Models\Battle;
use App\Models\BattleLog;
use App\Models\BattleMember;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

readonly class BattleState
{
	/**
	 * @param list<BattleMember> $members
	 * @param list<BattleMember> $opponents
	 * @param array<int, array{ability: Ability, available: bool}> $abilities
	 * @param Collection<int, BattleLog> $logs
	 * @param array{hits: int, blocks: int} $limits
	 */
	public function __construct(
		public Battle $battle,
		public BattleMember $fighter,
		public User $user,
		public ?BattleMember $opponent,
		public array $members,
		public array $opponents,
		public array $abilities,
		public ?Ability $activeAbility,
		public array $userItems,
		public array $opponentItems,
		public Collection $logs,
		public int $lastLogId,
		public array $limits,
		public CarbonImmutable $time,
		public int $timeoutLeft,
		public bool $roundExpired,
		public ?string $message,
	) {
	}
}
