<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserGiftResource;
use App\Models\Blocked;
use App\Models\LogsIp;
use App\Models\User;
use App\Models\UserFriend;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InfoController extends Controller
{
	public function index(Request $request, ?int $id = null): Response
	{
		$data = $request->validate([
			'login' => ['nullable', 'string', 'max:100'],
			'id' => ['nullable', 'integer', 'min:1'],
		]);

		$query = User::query()->with(['tribe', 'slots']);

		if ($id !== null) {
			$query->whereKey($id);
		} elseif (!empty($data['login'])) {
			$query->where('name', $data['login']);
		} elseif (!empty($data['id'])) {
			$query->whereKey($data['id']);
		} else {
			abort(404, 'Персонаж с таким логином или ID не найден!');
		}

		$user = $query->first();

		abort_unless($user !== null, 404, 'Персонаж с таким логином или ID не найден!');

		$user->calculate(false);

		$showAllGifts = $request->has('prizes');
		$gifts = $user->gifts()->with(['item', 'sender'])->orderByDesc('id');

		if (!$showAllGifts) {
			$gifts->limit(18);
		}

		$gifts = $gifts->get();
		$hasMoreGifts = !$showAllGifts && $gifts->count() > 17;
		$viewer = $request->user();
		$canViewPrivate = $viewer && (($viewer->rank >= 11 && $viewer->rank <= 14) || $viewer->rank === 36 || $viewer->rank >= 98);

		return Inertia::render('Info', [
			'person' => ProfileResource::make($user),
			'gifts' => UserGiftResource::collection($showAllGifts ? $gifts : $gifts->take(17)),
			'has_more_gifts' => $hasMoreGifts,
			'friends' => $this->friends($user),
			'friend_of' => $this->friends($user, true),
			'block_reason' => $user->blocked_at
				? Blocked::query()->whereBelongsTo($user)->latest('id')->value('reason')
				: null,
			'private_info' => $canViewPrivate ? [
				...$user->only(['email', 'ip', 'exp', 'gold', 'credits', 'updates']),
				'shared_ip_users' => $this->sharedIpUsers($user),
			] : null,
		]);
	}

	/** @return Collection<int, User> */
	private function friends(User $user, bool $incoming = false): Collection
	{
		$friends = UserFriend::query()
			->where($incoming ? 'friend_id' : 'user_id', $user->id)
			->where('is_ignored', false)
			->select($incoming ? 'user_id' : 'friend_id');

		return User::query()->whereIn('id', $friends)->orderBy('name')->get(['id', 'name']);
	}

	/** @return Collection<int, User> */
	private function sharedIpUsers(User $user): Collection
	{
		$addresses = LogsIp::query()->whereBelongsTo($user)->where('ip', '>', 0)->select('ip');
		$users = LogsIp::query()->whereIn('ip', $addresses)->select('user_id');

		return User::query()->whereIn('id', $users)->where('id', '!=', $user->id)->orderBy('name')->get(['id', 'name']);
	}
}
