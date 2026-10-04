<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\News;
use App\Models\Tribe;
use App\Models\User;
use App\Support\SocialLoginProviders;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class IndexController extends Controller
{
	public function index(): View
	{
		$players = User::query()
			->where('is_clone', false)
			->where(function (Builder $query) {
				$query->whereNull('blocked_at')->orWhere('blocked_at', '<=', now());
			})
			->where(function (Builder $query) {
				$query->whereNull('rank')->orWhereNotIn('rank', [60, 61, 100]);
			});

		return view('index', [
			'socialProviders' => SocialLoginProviders::available(),
			'news' => News::query()
				->orderByDesc('created_at')
				->orderByDesc('id')
				->paginate(5, ['id', 'title', 'text', 'author', 'created_at']),
			'topUsers' => (clone $players)
				->orderByDesc('rating')
				->orderByDesc('level')
				->orderBy('id')
				->limit(5)
				->get(['id', 'name', 'level', 'rating']),
			'topTribes' => Tribe::query()
				->orderByDesc('points')
				->orderBy('id')
				->limit(5)
				->get(['id', 'name', 'points']),
			'totalOnline' => (clone $players)->where('online', '>=', now()->subSeconds(180))->count(),
			'registeredToday' => (clone $players)->where('created_at', '>=', now()->startOfDay())->count(),
		]);
	}

	public function law(): View
	{
		return view('index.law');
	}

	public function agreement(): View
	{
		return view('index.agreement');
	}
}
