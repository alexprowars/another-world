<?php

namespace Database\Seeders;

use App\Models\Academy;
use Illuminate\Database\Seeder;

class AcademySeed extends Seeder
{
	public function run()
	{
		Academy::create([
			'title' => 'Лекарь',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::create([
			'title' => 'Кузнец',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::create([
			'title' => 'Огранщик',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::create([
			'title' => 'Наёмник',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::create([
			'title' => 'Шахтёр',
			'duration' => 43200,
			'price' => 500,
			'level' => 5,
		]);

		Academy::create([
			'title' => 'Маг',
			'duration' => 43200,
			'price' => 400,
			'level' => 5,
		]);

		Academy::create([
			'title' => 'Алхимик',
			'duration' => 43200,
			'price' => 500,
			'level' => 5,
		]);
	}
}
