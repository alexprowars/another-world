<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VaultSeed extends Seeder
{
	public function run(): void
	{
		$rooms = File::json(database_path('seeders/data/vaults.json'));

		DB::table('vaults')->insertOrIgnore($rooms);
	}
}
