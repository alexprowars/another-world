<?php

namespace App\Engine\Map;

use App\Exceptions\Exception;
use Inertia\Inertia;
use Throwable;

class Academy
{
	public function __invoke()
	{
		$user = auth()->user();

		if ($professionId = request()->integer('learn')) {
			try {
				$this->learn($professionId);
			} catch (Throwable $e) {
				flash($e->getMessage());
			}

			return back();
		}

		if ($user->r_type == 3 && $user->r_date?->isPast()) {
			$user->r_date = null;
			$user->r_type = null;
			$user->save();
		}

		$professions = [];

		$items = \App\Models\Academy::query()
			->orderBy('level')
			->get();

		foreach ($items as $item) {
			$professions[] = [
				'id' => $item->id,
				'title' => $item->title,
				'level' => $item->level,
				'duration' => $item->duration,
				'price' => $item->price,
			];
		}

		return Inertia::render('Map/Academy', [
			'professions' => $professions,
		]);
	}

	public function learn(int $professionId)
	{
		$item = \App\Models\Academy::query()
			->findOne($professionId);

		if (!$item) {
			throw new Exception('Академия не предоставляет таких услуг!');
		}

		$user = auth()->user();

		if (!$user->isFree()) {
			throw new Exception('Вы не можете заниматься сразу двумя делами!');
		}

		if ($user->gold < $item->price) {
			throw new Exception('Недостаточно кредитов!');
		}

		if ($user->level >= $item->level) {
			throw new Exception('Вы не можете получить эту профессию, уровень маловат!');
		}

		$user->r_date = now()->addSeconds($item->duration);
		$user->r_type = 3;
		$user->profession = $item->id;
		$user->gold -= $item->price;

		if ($user->save()) {
			throw new Exception('Процесс обучения начат! По окончанию обучения Вы станете высококвалицицированным специалистом!');
		}
	}
}
