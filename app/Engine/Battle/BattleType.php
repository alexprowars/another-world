<?php

namespace App\Engine\Battle;

enum BattleType: int
{
	case DUEL = 1;
	case GROUP = 2;
	case CHAOS = 3;
	case ALIGN = 4;
}
