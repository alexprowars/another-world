<?php

namespace Database\Seeders;

use App\Models\Craft;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CraftSeed extends Seeder
{
	public function run(): void
	{
		$recipes = [
			'elstr6' => ['pot1', 'pot8', 'pot5', 'elixir_empty'],
			'elstr8' => ['pot1', 'pot2', 'pot4', 'elixir_empty'],
			'elagil6' => ['pot1', 'pot8', 'almaz', 'elixir_empty'],
			'elagil8' => ['pot1', 'pot10', 'almaz', 'elixir_empty'],
			'eldex6' => ['pot1', 'amazonit', 'pot6', 'elixir_empty'],
			'eldex8' => ['pot1', 'amazonit', 'pot3', 'elixir_empty'],
			'addustal' => ['pot7', 'pot1', 'pot10', 'pot6', 'elixir_empty'],
		];

		DB::transaction(function () use ($recipes) {
			foreach ($recipes as $code => $ingredients) {
				$item = Item::query()->where('code', $code)->firstOrFail();

				foreach ($ingredients as $ingredient) {
					Item::query()->where('code', $ingredient)->firstOrFail();
				}

				Craft::query()->updateOrCreate(
					['item_id' => $item->id],
					['ingredients' => array_count_values($ingredients)]
				);
			}
		});
	}
}
