<?php

namespace App\Http\Resources;

use App\Facades\Vars;
use App\Models\Level;
use App\Models\User;
use App\Services\UserService;
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
		$photo = Cache::remember('media::user_' . $this->resource->id, 3600, function () {
			return $this->resource->getFirstMediaUrl(conversionName: 'thumb');
		});

		$user = $this->resource;

		$up = Level::query()
			->select(['l1.up', 'l2.exp'])
			->from('levels as l1')
			->join('levels as l2', 'l2.id', '=', DB::raw('l1.id + 1'))
			->where('l1.up', $user->up)
			->where('l1.level', $user->level)
			->toBase()
			->first();

		$user->calculate();

		$data = [
			'id' => $user->id,
			'name' => $user->nickname,
			'email' => $user->email,
			'avatar' => $user->obraz,
			'rank' => $user->rank,
			'vip' => $user->vip?->isFuture() ?? false,
			'room' => $user->room,
			'admin' => $user->isAdmin(),
			'gender' => $user->gender,
			'level' => $user->level,
			'slots' => $user->getSlotsInfo(),
			'experience' => $user->experience,
			'profession' => $user->profession,
			'moneys' => $user->moneys,
			'credits' => $user->credits,
			'updates' => $user->updates,
			'tribe' => null,
			'hp_now' => (int) floor($user->hp_now ?: 0),
			'hp_max' => $user->hp_max ?: 0,
			'energy_now' => (int) floor($user->energy_now ?: 0),
			'energy_max' => $user->energy_max ?: 0,
			'ustal_now' => (int) floor($user->ustal_now ?: 0),
			'ustal_max' => $user->ustal_max ?: 0,
			'krit' => $user->krit,
			'mkrit' => $user->mkrit,
			'unkrit' => $user->unkrit,
			'uv' => $user->uv,
			'unuv' => $user->unuv,
			'pblock' => $user->pblock,
			'mblock' => $user->mblock,
			'pbr' => $user->pbr,
			'kbr' => $user->kbr,
			'br1' => $user->br1,
			'br2' => $user->br2,
			'br3' => $user->br3,
			'br4' => $user->br4,
			'br5' => $user->br5,
			'damage_min' => $user->strength / 3 + $user->min,
			'damage_max' => 1 + $user->strength / 1.5 + $user->max,
			'magic_min' => $user->razum / 1.5,
			'magic_max' => 1 + $user->razum,
			'level_up' => $up,
			'otravl' => $user->otravl,
		];

		if ($user->tribe) {
			$data['tribe'] = [
				'id' => $user->tribe->id,
				'name' => $user->tribe->name,
			];
		}

		$rating = UserService::getUserRaiting($user);

		if ($user->rating != $rating) {
			$user->rating = $rating;
			$user->save();
		}

		$data['rating'] = $user->rating;

		foreach (Vars::getStats() as $stat) {
			$data[$stat] = $user->{$stat};
		}

		return $data;
	}
}
