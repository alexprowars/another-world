<?php

namespace App\Http\Resources;

use App\Facades\Vars;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property User $resource */
class ProfileResource extends JsonResource
{
	public function toArray($request): array
	{
		$user = $this->resource;

		return [
			...$user->only(['id', 'name', 'level', 'rank', 'profession', 'wins', 'losses', 'draws', 'rating', 'gender', 'city', 'about']),
			'avatar' => $user->getAvatar(),
			'slots' => $user->getSlotsInfo(),
			'hp_now' => (int) round($user->hp_now),
			'hp_max' => $user->hp_max,
			'energy_now' => (int) round($user->energy_now),
			'energy_max' => $user->energy_max,
			'stamina_now' => (int) round($user->stamina_now),
			'stamina_max' => $user->stamina_max,
			'stats' => collect(Vars::getStats())->map(fn (string $stat) => [
				'code' => $stat,
				'value' => $user->{$stat},
				'base' => $user->{'s_' . $stat},
			])->values(),
			'tribe' => $user->tribe?->only(['id', 'name', 'short']),
			'created_at' => $user->created_at?->toAtomString(),
			'zodiac' => $this->zodiac(),
			'admin' => $user->isAdmin(),
			'blocked' => $user->blocked_at !== null,
			'prison' => $user->prison?->isFuture() ? $user->prison->toAtomString() : null,
			'prison_reason' => $user->prison?->isFuture() ? $user->prison_reason : null,
			'silence_until' => $user->silence?->isFuture() ? $user->silence->toAtomString() : null,
			'injury_until' => $user->injury?->isFuture() ? $user->injury->toAtomString() : null,
			'battle_fury' => $user->battle_fury?->isFuture()
				? $user->battle_fury->toAtomString()
				: null,
			'vip' => $user->vip?->isFuture() ?? false,
		];
	}

	private function zodiac(): ?array
	{
		$date = $this->resource->created_at;

		if (!$date) {
			return null;
		}

		$lastDays = [20, 18, 20, 20, 21, 22, 22, 21, 23, 23, 21, 22];
		$names = ['Козерог — Земля', 'Водолей — Воздух', 'Рыбы — Вода', 'Овен — Огонь', 'Телец — Земля', 'Близнецы — Воздух', 'Рак — Вода', 'Лев — Огонь', 'Дева — Земля', 'Весы — Воздух', 'Скорпион — Вода', 'Стрелец — Огонь'];
		$index = ($date->month - 1 + ($date->day > $lastDays[$date->month - 1] ? 1 : 0)) % 12;

		return ['id' => $index + 1, 'name' => $names[$index]];
	}
}
