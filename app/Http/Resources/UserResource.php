<?php

namespace App\Http\Resources;

use App\Engine\Services\UserService;
use App\Facades\Vars;
use App\Models\Effect;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @mixin User
 * @property User $resource
 */
class UserResource extends JsonResource
{
	public function toArray($request): array
	{
		//$photo = Cache::remember('media::user_' . $this->resource->id, 3600, function () {
		//	return $this->resource->getFirstMediaUrl(conversionName: 'thumb');
		//});

		$user = $this->resource;

		$combatStats = $user->getCombatStats();

		$up = Level::query()
			->select(['levels.up', 'l2.exp'])
			->join('levels as l2', 'l2.id', '=', DB::raw('levels.id + 1'))
			->where('levels.up', $user->up)
			->where('levels.level', $user->level)
			->toBase()
			->first();

		$data = [
			'id' => $user->id,
			'name' => $user->name,
			'email' => $user->email,
			'image' => $user->image,
			'avatar' => $user->getAvatar(),
			'rank' => $user->rank,
			'vip' => $user->vip?->isFuture() ?? false,
			'location' => $user->location,
			'admin' => $user->isAdmin(),
			'gender' => $user->gender,
			'level' => $user->level,
			'level_up' => $up,
			'slots' => $user->getSlotsInfo(),
			'statuses' => $this->statuses(),
			'exp' => $user->exp,
			'profession' => $user->profession,
			'gold' => $user->gold,
			'credits' => $user->credits,
			'updates' => $user->updates,
			'tribe' => null,
			'hp_now' => (int) floor($user->hp_now ?: 0),
			'hp_max' => $user->hp_max ?: 0,
			'hp_regeneration' => $this->healthRegeneration(),
			'energy_now' => (int) floor($user->energy_now ?: 0),
			'energy_max' => $user->energy_max ?: 0,
			'energy_regeneration' => $this->energyRegeneration(),
			'stamina_now' => (int) floor($user->stamina_now ?: 0),
			'stamina_max' => $user->stamina_max ?: 0,
			'krit' => $combatStats->krit,
			'mkrit' => $combatStats->mkrit,
			'unkrit' => $combatStats->unkrit,
			'uv' => $combatStats->uv,
			'unuv' => $combatStats->unuv,
			'pblock' => $combatStats->pblock,
			'mblock' => $combatStats->mblock,
			'pbr' => $combatStats->pbr,
			'kbr' => $combatStats->kbr,
			'armor1' => $combatStats->armor1,
			'armor2' => $combatStats->armor2,
			'armor3' => $combatStats->armor3,
			'armor4' => $combatStats->armor4,
			'armor5' => $combatStats->armor5,
			'damage_min' => round($combatStats->getMinDamage()),
			'damage_max' => round($combatStats->getMaxDamage()),
			'magic_min' => $combatStats->getMinMagicDamage(),
			'magic_max' => $combatStats->getMaxMagicDamage(),
			'poison' => $user->poison,
			'injury' => $user->injury?->toAtomString(),
			'r_date' => $user->r_date?->toAtomString(),
			'r_type' => $user->r_type,
		];

		if ($user->tribe) {
			$data['tribe'] = [
				'id' => $user->tribe->id,
				'name' => $user->tribe->name,
			];
		}

		$user->save();

		$data['rating'] = $user->rating;
		$data['base_stats'] = [];

		foreach (Vars::getStats() as $stat) {
			$data[$stat] = $combatStats->{$stat};
			$data['base_stats'][$stat] = $user->{$stat};
		}

		return $data;
	}

	private function healthRegeneration(): ?array
	{
		$user = $this->resource;

		$duration = UserService::getHealthRegenerationTime($user);

		if ($duration === null || $user->hp_now >= $user->hp_max) {
			return null;
		}

		$hospital = $user->r_type == 2;

		if ($hospital) {
			$remaining = now()->diffInSeconds($user->r_date);
		} else {
			$elapsed = max(0, (int) $user->online->diffInSeconds());
			$remaining = (1 - $user->hp_now / $user->hp_max) * $duration - $elapsed;
		}

		return [
			'duration' => $duration,
			'remaining' => max(0, $remaining),
			'hospital' => $hospital,
		];
	}

	private function energyRegeneration(): ?array
	{
		$user = $this->resource;

		$duration = UserService::getEnergyRegenerationTime($user);

		if ($duration === null || $user->energy_now >= $user->energy_max) {
			return null;
		}

		$elapsed = max(0, (int) $user->online->diffInSeconds());
		$remaining = (1 - $user->energy_now / $user->energy_max) * $duration - $elapsed;

		return [
			'duration' => $duration,
			'remaining' => max(0, $remaining),
			'hospital' => false,
		];
	}

	private function statuses(): array
	{
		$statuses = [];

		$attributes = [
			'silence' => ['Чат', 'Запрещено общение в чате'],
			'injury' => ['Травма', 'Персонаж травмирован'],
			'invisible' => ['Тень', 'Невидимость'],
			'battle_fury' => ['Ярость', 'Боевая ярость: опыт в боях увеличен в 2 раза'],
			'attack_protection' => ['Защита', 'Защита от нападения'],
			'magic_protection' => ['Защита', 'Защита от магии'],
			'vampire_protection' => ['Защита', 'Защита от вампиров'],
		];

		foreach ($attributes as $attribute => [$label, $title]) {
			$until = $this->resource->{$attribute};

			if (!$until?->isFuture()) {
				continue;
			}

			$statuses[] = [
				'id' => $attribute,
				'label' => $label,
				'title' => $title,
				'until' => $until->toAtomString(),
			];
		}

		$effects = $this->resource->effects;

		foreach ($effects as $effect) {
			$label = match ($effect->type) {
				Effect::AURA => 'Аура',
				Effect::POTION => 'Зелье',
				Effect::INJURY => 'Травма',
				Effect::POISON => 'Отравление',
				default => null,
			};

			if (!$label) {
				continue;
			}

			$statuses[] = [
				'id' => 'effect_' . $effect->id,
				'label' => $label,
				'title' => $label,
				'until' => $effect->date?->toAtomString(),
			];
		}

		return $statuses;
	}
}
