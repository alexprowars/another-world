<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class AvatarController extends Controller
{
	public function index(Request $request)
	{
		if ($request->has('image')) {
			try {
				if ($this->user->image) {
					throw new Exception('Вы не можете установить образ!');
				}

				$image = $request->integer('image');

				if ($image && $image < 6) {
					$this->user->image = 'images/' . ($this->user->gender == 'F' ? 2 : 1) . '/' . $image . '.png';
					$this->user->update();

					throw new Exception('Образ установлен!');
				}
			} catch (Throwable $e) {
				Inertia::flash(['message' => $e->getMessage()]);

				return back();
			}
		}

		return Inertia::render('Person/Avatar', [
			'images' => [1, 2, 3, 4, 5],
		]);
	}
}
