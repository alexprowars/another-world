<?php

namespace App\Engine\Magic\Spells;

use App\Engine\Services\InventoryService;
use App\Models\User;
use App\Models\UserItem;

class Strip extends AbstractSpell
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
		InventoryService::unsetAllObject($target);

		return $caster->name . ' использовал «' . $item->title . '». С персонажа ' . $target->name . ' снята вся экипировка.';
	}
}
