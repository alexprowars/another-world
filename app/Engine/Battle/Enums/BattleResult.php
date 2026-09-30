<?php

namespace App\Engine\Battle\Enums;

enum BattleResult: int
{
	case DRAW = 1;
	case FIRST_TEAM_WIN = 3;
	case SECOND_TEAM_WIN = 2;

	public function forSide(int $side): ParticipantResult
	{
		return match ($this) {
			self::DRAW => ParticipantResult::DRAW,
			self::FIRST_TEAM_WIN => $side === 0 ? ParticipantResult::WIN : ParticipantResult::LOSS,
			self::SECOND_TEAM_WIN => $side === 1 ? ParticipantResult::WIN : ParticipantResult::LOSS,
		};
	}
}
