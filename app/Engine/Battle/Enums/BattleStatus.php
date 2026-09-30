<?php

namespace App\Engine\Battle\Enums;

enum BattleStatus: string
{
	case WAITING = 'waiting';
	case ACTIVE = 'active';
	case FINISHED = 'finished';
	case CANCELLED = 'cancelled';
}
