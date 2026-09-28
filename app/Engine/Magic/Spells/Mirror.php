<?php

namespace App\Engine\Magic\Spells;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\UserItem;
use App\Services\BattleService;

class Mirror extends AbstractSpell
{
	public function __construct()
	{
		parent::__construct(10);
	}

	public function cast(User $caster, User $target, UserItem $item): string
	{
		if (!$caster->is($target)) {
			throw new Exception('Клона можно вызвать только для себя');
		}

		BattleService::fightMirror($caster);

		return 'Вы использовали «' . $item->title . '». Начался бой с вашим клоном.';
	}
}
