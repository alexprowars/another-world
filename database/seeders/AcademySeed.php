<?php

namespace Database\Seeders;

use App\Models\Academy;
use Illuminate\Database\Seeder;

class AcademySeed extends Seeder
{
	public function run()
	{
		Academy::query()->updateOrCreate(['id' => 1], [
			'title' => 'Лекарь',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::query()->updateOrCreate(['id' => 2], [
			'title' => 'Кузнец',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::query()->updateOrCreate(['id' => 3], [
			'title' => 'Огранщик',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::query()->updateOrCreate(['id' => 4], [
			'title' => 'Наёмник',
			'duration' => 43200,
			'price' => 600,
			'level' => 5,
		]);

		Academy::query()->updateOrCreate(['id' => 5], [
			'title' => 'Шахтёр',
			'duration' => 43200,
			'price' => 500,
			'level' => 5,
		]);

		Academy::query()->updateOrCreate(['id' => 6], [
			'title' => 'Маг',
			'duration' => 43200,
			'price' => 400,
			'level' => 5,
		]);

		Academy::query()->updateOrCreate(['id' => 7], [
			'title' => 'Алхимик',
			'duration' => 43200,
			'price' => 500,
			'level' => 5,
		]);
	}
}
