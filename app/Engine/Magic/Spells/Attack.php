<?php

namespace App\Engine\Magic\Spells;

use App\Engine\Services\BattleService;
use App\Models\User;
use App\Models\UserItem;

class Attack extends AbstractSpell
{
	public function __construct(private readonly bool $blood = false)
	{
		parent::__construct();
	}

	public function isOffensive(): bool
	{
		return true;
	}

	public function canJoinBattle(): bool
	{
		return true;
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		BattleService::attackWithMagic($caster, $target, $this->blood);

		return $caster->name . ' использовал «' . $item->title . '» и напал на персонажа ' . $target->name . '.';
	}
}
