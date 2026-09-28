<?php

namespace App\Engine\Magic\Spells;

use App\Models\User;
use App\Models\UserItem;
use App\Services\BattleService;

class ChangeSide extends AbstractSpell
{
	public function __construct()
	{
		parent::__construct(20);
	}

	public function isOffensive(): bool
	{
		return true;
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		BattleService::changeSideWithMagic($caster, $target);

		return $caster->name . ' переманил персонажа ' . $target->name . ' на свою сторону.';
	}
}
