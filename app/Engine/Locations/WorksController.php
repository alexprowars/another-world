<?php

namespace App\Engine\Locations;

use App\Exceptions\Exception;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkType;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorksController extends LocationController
{
	public function index()
	{
		$user = auth()->user();

		if ($user->r_type == 4 && $user->r_date?->isPast()) {
			DB::transaction(function () use ($user) {
				$user->refreshForUpdate();

				abort_if($user->trashed() || !$user->currentLocation()->is('works'), 404);

				if ($user->r_type == 4 && $user->r_date?->isPast()) {
					$user->update([
						'gold' => $user->gold + ($user->work()->value('price') ?? 0),
						'work_id' => null,
						'r_date' => null,
						'r_type' => null,
					]);

					flash('Работа завершена! Зарплата начислена.');
				}
			});
		}

		$types = WorkType::query()
			->with(['works' => fn ($query) => $query->orderBy('duration')])
			->orderBy('id')
			->get();

		return Inertia::render('Map/Works', [
			'types' => $types,
			'salary' => $user->r_type == 4 ? $user->work()->value('price') : null,
		]);
	}

	public function store()
	{
		$data = request()->validate([
			'work_id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$this->start($this->user, (int) $data['work_id']);

			flash('Процесс работы начат! По окончании работы Вам выплатят зарплату.');
		} catch (Exception $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation();
	}

	private function start(User $user, int $workId): void
	{
		DB::transaction(function () use ($user, $workId) {
			$user->refreshForUpdate();

			abort_if($user->trashed() || !$user->currentLocation()->is('works'), 404);

			if ($user->r_date || $user->r_type) {
				throw new Exception('Вы не можете заниматься сразу двумя делами!');
			}

			$work = Work::query()
				->with('type')
				->find($workId);

			if (!$work || !$work->type) {
				throw new Exception('Центр занятости не предоставляет таких услуг!');
			}

			if ($user->level < $work->type->level) {
				throw new Exception('Вы не можете получить эту работу, уровень маловат!');
			}

			$activity = $work->duration / 3600 * $work->type->activity;

			if ($user->stamina_now < $activity) {
				throw new Exception('Недостаточно сил для работы. Восстановите запас сил в боях.');
			}

			$user->update([
				'r_date' => now()->addSeconds($work->duration),
				'r_type' => 4,
				'work_id' => $work->id,
				'stamina_now' => $user->stamina_now - $activity,
			]);
		});
	}
}
