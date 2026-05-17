<?php

namespace App\Engine\Battle;

enum BattleStatus: string
{
	case WAITING = 'waiting';
	case ACTIVE = 'active';
	case FINISHED = 'finished';
	case CANCELLED = 'cancelled';
}
