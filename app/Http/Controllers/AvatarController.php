<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Throwable;

class AvatarController extends Controller
{
	public function index(Request $request)
	{
		$images = [1, 2, 3, 4, 5];

		if ($request->isMethod('post')) {
			$data = $request->validate([
				'image' => ['required', 'integer', Rule::in($images)],
			], [
				'image.*' => 'Выберите один из пяти бесплатных образов.',
			]);

			try {
				DB::transaction(function () use ($request, $data) {
					$user = User::query()
						->lockForUpdate()
						->findOrFail($request->user()->id);

					$imageId = (int) pathinfo($user->image ?? '', PATHINFO_FILENAME);

					if ($imageId >= 1 && $imageId <= 49) {
						throw new Exception('Вы не можете установить образ!');
					}

					$path = 'images/' . ($user->gender === 'F' ? 2 : 1) . '/' . $data['image'] . '.jpg';

					$user->update(['image' => $path]);
				}, 3);

				Inertia::flash(['message' => 'Образ установлен!']);
			} catch (Throwable $e) {
				Inertia::flash(['message' => $e->getMessage()]);
			}

			return to_route('person.avatar');
		}

		return Inertia::render('Person/Avatar', [
			'images' => $images,
		]);
	}
}
