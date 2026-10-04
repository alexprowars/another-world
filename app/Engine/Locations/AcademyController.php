<?php

namespace App\Engine\Locations;

use App\Exceptions\Exception;
use App\Models\Academy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable;

class AcademyController extends LocationController
{
	public function index()
	{
		$user = auth()->user();

		if ($user->r_type == 3 && $user->r_date?->isPast()) {
			$user->r_date = null;
			$user->r_type = null;
			$user->save();
		}

		$professions = [];

		$items = Academy::query()
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

	public function learn(Request $request)
	{
		$data = $request->validate([
			'profession_id' => ['required', 'integer', 'min:1'],
		]);

		try {
			$this->startLearning((int) $data['profession_id']);
		} catch (Throwable $e) {
			flash($e->getMessage());
		}

		return $this->redirectToLocation();
	}

	private function startLearning(int $professionId)
	{
		$item = Academy::query()
			->findOne($professionId);

		if (!$item) {
			throw new Exception('Академия не предоставляет таких услуг!');
		}

		$user = auth()->user();

		$saved = DB::transaction(function () use ($user, $item) {
			$user->refreshForUpdate();

			if (!$user->isFree()) {
				throw new Exception('Вы не можете заниматься сразу двумя делами!');
			}

			if ($user->gold < $item->price) {
				throw new Exception('Недостаточно кредитов!');
			}

			if ($user->level < $item->level) {
				throw new Exception('Вы не можете получить эту профессию, уровень маловат!');
			}

			$user->r_date = now()->addSeconds($item->duration);
			$user->r_type = 3;
			$user->profession = $item->id;
			$user->gold -= $item->price;

			return $user->save();
		});

		if ($saved) {
			throw new Exception('Процесс обучения начат! По окончанию обучения Вы станете высококвалицицированным специалистом!');
		}
	}
}
