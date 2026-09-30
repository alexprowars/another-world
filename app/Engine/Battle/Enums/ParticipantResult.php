<?php

namespace App\Engine\Battle\Enums;

enum ParticipantResult: string
{
	case WIN = 'win';
	case LOSS = 'lose';
	case DRAW = 'draw';
}
