<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Items;
use App\Models\Level;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
	public function run()
	{
		$path = database_path('sql/levels.sql');

		if (File::exists($path)) {
			$exist = Level::query()->exists();

			if (!$exist) {
				DB::transaction(function () use ($path) {
					DB::unprepared(File::get($path));
				});
			}
		}

		$path = database_path('sql/items.sql');

		if (File::exists($path)) {
			$exist = Item::query()->exists();

			if (!$exist) {
				DB::transaction(function () use ($path) {
					DB::unprepared(File::get($path));
				});
			}
		}

		$path = database_path('sql/shops.sql');

		if (File::exists($path)) {
			$exist = Shop::query()->exists();

			if (!$exist) {
				DB::transaction(function () use ($path) {
					DB::unprepared(File::get($path));
				});
			}
		}

		$this->call(PermissionSeed::class);
		$this->call(RoleSeed::class);
		$this->call(UserSeed::class);
		$this->call(BotsSeed::class);
	}
}
