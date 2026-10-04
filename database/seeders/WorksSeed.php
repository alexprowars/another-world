<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorksSeed extends Seeder
{
	public function run(): void
	{
		DB::transaction(function () {
			DB::table('work_types')->insertOrIgnore([
				['id' => 1, 'title' => 'Легкая работа', 'level' => 0, 'activity' => 20],
				['id' => 2, 'title' => 'Тяжелая работа', 'level' => 2, 'activity' => 30],
			]);

			DB::table('works')->insertOrIgnore([
				[
					'id' => 1,
					'work_type_id' => 1,
					'title' => 'Уборщик',
					'duration' => 3600,
					'price' => 3,
				],
				[
					'id' => 2,
					'work_type_id' => 1,
					'title' => 'Поливка цветов',
					'duration' => 7200,
					'price' => 6,
				],
				[
					'id' => 3,
					'work_type_id' => 2,
					'title' => 'Строительство',
					'duration' => 10800,
					'price' => 12,
				],
			]);
		});
	}
}
