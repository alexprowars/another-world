<?php

namespace App\Engine\Magic;

use App\Models\User;
use App\Models\UserItem;

interface Spell
{
	public function manaCost(): int;

	public function isOffensive(): bool;

	public function canJoinBattle(): bool;

	public function ignoresMagicProtection(): bool;

	/** Применяет эффект к проверенным участникам внутри транзакции MagicService. */
	public function cast(User $caster, User $target, UserItem $item): string;
}
